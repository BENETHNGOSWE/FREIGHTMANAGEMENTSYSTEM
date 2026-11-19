<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\Accounting\ChartOfAccounts;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The accounts module, exercised through the real HTTP layer: every page renders, the forms
 * post, the books stay balanced and one tenant can never see another tenant's accounts.
 */
class AccountsModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('accounts') || ! Schema::hasTable('journal_entries')) {
            $this->markTestSkipped('Run `php artisan migrate` before the accounts tests.');
        }
    }

    private function user(): User
    {
        $email = 'accounts-test@example.test';
        $user = User::whereEmail($email)->first();

        if (! $user) {
            $type = ClientType::firstOrCreate(['name' => 'Freight']);
            $client = Client::firstOrCreate(
                ['client_email' => 'accounts-client@example.test'],
                [
                    'client_name' => 'Accounts Test Co', 'client_location' => 'Dar es Salaam',
                    'client_city' => 'Dar es Salaam', 'client_country' => 'Tanzania',
                    'client_type_id' => $type->id,
                ]
            );

            $user = User::create([
                'name' => 'Accounts Test', 'email' => $email,
                'password' => bcrypt('secret'), 'client_id' => $client->id,
            ]);

            Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole('admin');
        }

        // A client always has the standard chart, created on first use.
        app(ChartOfAccounts::class)->ensureForClient((int) $user->client_id);

        return $user;
    }

    private function account(User $user, string $name): Account
    {
        return Account::where('client_id', $user->client_id)->where('name', $name)->firstOrFail();
    }

    public function test_every_accounts_page_opens(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $clientId = (int) $user->client_id;
        $account = Account::where('client_id', $clientId)->where('is_group', false)->firstOrFail();

        $entry = JournalEntry::forClient($clientId)->first()
            ?? app(\App\Services\Accounting\LedgerService::class)->post($clientId, [
                'posting_date' => now()->toDateString(),
                'remarks' => 'Seed entry for the page test',
                'lines' => [
                    ['account_id' => $this->account($user, 'Cash in Hand')->id, 'debit' => 1000],
                    ['account_id' => $this->account($user, 'Owner Capital')->id, 'credit' => 1000],
                ],
            ]);

        $customer = Customer::where('client_id', $clientId)->first()
            ?? Customer::create([
                'customer_firstname' => 'Page Test', 'phone' => '0700', 'user_id' => $user->id,
                'client_id' => $clientId,
            ]);

        $pages = [
            route('accounts.dashboard'),
            route('accounts.chart.index'),
            route('accounts.chart.create'),
            route('accounts.journal.index'),
            route('accounts.journal.create'),
            route('accounts.ledger.index'),
            route('accounts.ledger.index', ['account_id' => $account->id]),
            route('accounts.ledger.customer', $customer),
            route('accounts.reports.trial_balance'),
            route('accounts.reports.profit_loss'),
            route('accounts.reports.balance_sheet'),
            route('accounts.reports.receivables'),
            route('accounts.reports.cash_bank'),
            route('accounts.settings'),
            route('accounts.journal.show', $entry),
            route('accounts.chart.edit', $account),
        ];

        foreach ($pages as $url) {
            $response = $this->get($url);
            $this->assertSame(200, $response->getStatusCode(), 'Failed page: ' . $url . ' -> ' . $response->getStatusCode());
        }
    }

    public function test_csv_export_downloads(): void
    {
        $this->actingAs($this->user());

        $this->get(route('accounts.reports.export', ['report' => 'trial-balance']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_a_manual_journal_entry_can_be_posted_from_the_form(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $reference = 'Feature test entry ' . uniqid();

        $this->post(route('accounts.journal.store'), [
            'posting_date' => now()->toDateString(),
            'entry_type' => 'Journal Entry',
            'remarks' => $reference,
            'lines' => [
                ['account_id' => $this->account($user, 'Cash in Hand')->id, 'debit' => 25000, 'credit' => 0],
                ['account_id' => $this->account($user, 'Other Income')->id, 'debit' => 0, 'credit' => 25000],
            ],
        ])->assertRedirect();

        $entry = JournalEntry::where('remarks', $reference)->first();

        $this->assertNotNull($entry);
        $this->assertSame('posted', $entry->status);
        $this->assertEquals(25000, (float) $entry->total_debit);
        $this->assertEquals(25000, (float) $entry->total_credit);
    }

    public function test_an_unbalanced_entry_is_refused_with_a_message(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $reference = 'Unbalanced attempt ' . uniqid();

        $this->from(route('accounts.journal.create'))->post(route('accounts.journal.store'), [
            'posting_date' => now()->toDateString(),
            'entry_type' => 'Journal Entry',
            'remarks' => $reference,
            'lines' => [
                ['account_id' => $this->account($user, 'Cash in Hand')->id, 'debit' => 500],
                ['account_id' => $this->account($user, 'Other Income')->id, 'credit' => 400],
            ],
        ])->assertRedirect(route('accounts.journal.create'))->assertSessionHas('error');

        $this->assertNull(JournalEntry::where('remarks', $reference)->first());
    }

    public function test_a_new_account_can_be_created_from_the_form(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $name = 'Test Marketing ' . uniqid();

        $this->post(route('accounts.chart.store'), [
            'name' => $name,
            'root_type' => 'Expense',
            'account_type' => 'Expense Account',
            'account_number' => '5999',
        ])->assertRedirect(route('accounts.chart.index'));

        $this->assertDatabaseHas('accounts', [
            'name' => $name,
            'client_id' => $user->client_id,
            'root_type' => 'Expense',
        ]);
    }

    public function test_another_tenants_account_is_invisible(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $otherType = ClientType::firstOrCreate(['name' => 'Retail']);
        $other = Client::firstOrCreate(
            ['client_email' => 'accounts-other@example.test'],
            [
                'client_name' => 'Other Co', 'client_location' => 'Dar', 'client_city' => 'Dar',
                'client_country' => 'Tanzania', 'client_type_id' => $otherType->id,
            ]
        );
        app(ChartOfAccounts::class)->ensureForClient((int) $other->id);

        $foreign = Account::where('client_id', $other->id)->firstOrFail();

        $this->get(route('accounts.chart.edit', $foreign))->assertNotFound();
    }

    public function test_a_visitor_is_redirected_to_login(): void
    {
        $this->get(route('accounts.dashboard'))->assertRedirect();
    }

    public function test_settings_can_be_saved_and_the_chart_re_seeded(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $cash = $this->account($user, 'Cash in Hand');
        $bank = $this->account($user, 'CRDB Bank');

        $this->put(route('accounts.settings.update'), [
            'receivable_account_id' => $this->account($user, 'Accounts Receivable')->id,
            'income_account_id' => $this->account($user, 'Freight Income')->id,
            'transport_income_account_id' => $this->account($user, 'Transport Income')->id,
            'cash_account_id' => $cash->id,
            'bank_account_id' => $bank->id,
            'fiscal_year_start_month' => 1,
            'auto_post_invoices' => 1,
            'auto_post_payments' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('accounting_settings', [
            'client_id' => $user->client_id,
            'cash_account_id' => $cash->id,
            'bank_account_id' => $bank->id,
        ]);

        $this->post(route('accounts.settings.seed'))->assertRedirect();
        $this->post(route('accounts.settings.post_existing'))->assertRedirect();

        $before = JournalEntry::where('client_id', $user->client_id)->count();
        $this->post(route('accounts.settings.post_existing'))->assertRedirect();
        $this->assertSame($before, JournalEntry::where('client_id', $user->client_id)->count());
    }

    public function test_an_invoice_and_its_payment_reach_the_reports(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $customer = Customer::firstOrCreate(
            ['customer_firstname' => 'Ledger Test', 'client_id' => $user->client_id],
            ['phone' => '0711', 'user_id' => $user->id]
        );

        $invoice = Invoice::create([
            'invoice_no' => 'INV-TEST-' . uniqid(),
            'prefix' => 'INV',
            'total_amount' => 2000000,
            'total_transportfee' => 0,
            'total_balance' => 2000000,
            'status' => 0,
            'customer_id' => $customer->id,
            'client_id' => $user->client_id,
            'cbm_price' => 200,
            'exchange_rate' => 2500,
        ]);

        $entry = JournalEntry::where('reference_doctype', 'Invoice')->where('reference_id', $invoice->id)->first();
        $this->assertNotNull($entry, 'the invoice should post itself');
        $this->assertEquals(2000000, (float) $entry->total_debit);

        \App\Models\InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'client_id' => $user->client_id,
            'amount_received' => 500000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'BANK',
            'received_by' => $user->id,
        ]);

        $reports = app(\App\Services\Accounting\AccountingReports::class);
        $tb = $reports->trialBalance((int) $user->client_id, now()->startOfYear()->toDateString(), now()->toDateString());

        $this->assertEqualsWithDelta($tb['totals']['debit'], $tb['totals']['credit'], 0.01, 'trial balance must balance');

        $receivable = $reports->receivables((int) $user->client_id);
        $this->assertEqualsWithDelta(1500000, $receivable['totals']['balance'], 0.01);
    }

    public function test_a_brand_new_client_gets_a_full_chart_on_first_visit(): void
    {
        $type = ClientType::firstOrCreate(['name' => 'Freight']);
        $client = Client::create([
            'client_name' => 'Fresh Co', 'client_location' => 'Dar', 'client_city' => 'Dar',
            'client_country' => 'Tanzania', 'client_email' => 'fresh-' . uniqid() . '@example.test',
            'client_type_id' => $type->id,
        ]);

        $user = User::create([
            'name' => 'Fresh Admin', 'email' => 'fresh-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'), 'client_id' => $client->id,
        ]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('accounts.dashboard'))->assertOk();
        $this->assertGreaterThanOrEqual(35, Account::where('client_id', $client->id)->count());

        $this->get(route('accounts.reports.balance_sheet'))->assertOk();
        $this->get(route('accounts.reports.receivables'))->assertOk();
    }
}
