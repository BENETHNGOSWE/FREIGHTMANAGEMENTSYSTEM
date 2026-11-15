<?php

namespace App\Http\Controllers\Accounting;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChartOfAccountsController extends AccountingController
{
    public function index(Request $request): View
    {
        $this->allow();

        $clientId = $this->clientId();
        $this->chart()->ensureForClient($clientId);

        $accounts = Account::forClient($clientId)
            ->with('parent')
            ->orderByRaw('coalesce(account_number, name) asc')
            ->orderBy('name')
            ->get();

        $search = trim((string) $request->get('search', ''));

        if ($search !== '') {
            $accounts = $accounts->filter(function (Account $account) use ($search) {
                return str_contains(mb_strtolower($account->name), mb_strtolower($search))
                    || str_contains((string) $account->account_number, $search);
            })->values();
        }

        return view('dashboards.accounts.chart.index', [
            'accounts' => $accounts,
            'search' => $search,
            'rootTypes' => Account::ROOT_TYPES,
            'grouped' => collect(Account::ROOT_TYPES)->mapWithKeys(
                fn ($root) => [$root => $accounts->where('root_type', $root)->sortBy('account_number')]
            ),
        ]);
    }

    public function create(): View
    {
        $this->allow('create accounts');
        $clientId = $this->clientId();
        $this->chart()->ensureForClient($clientId);

        return view('dashboards.accounts.chart.form', [
            'account' => new Account(['root_type' => 'Asset', 'currency' => 'TZS', 'client_id' => $clientId]),
            'parents' => Account::forClient($clientId)->where('is_group', true)->orderBy('account_number')->get(),
            'rootTypes' => Account::ROOT_TYPES,
            'accountTypes' => Account::ACCOUNT_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->allow('create accounts');

        $clientId = $this->clientId();
        $data = $this->validated($request, $clientId);
        $data['client_id'] = $clientId;

        if (! empty($data['parent_id'])) {
            $parent = Account::forClient($clientId)->findOrFail($data['parent_id']);
            $data['root_type'] = $parent->root_type;
        }

        Account::create($data);

        return redirect()
            ->route('accounts.chart.index')
            ->with('success', 'Account created.');
    }

    public function edit(Account $account): View
    {
        $this->allow('update accounts');
        $clientId = $this->clientId();
        abort_unless((int) $account->client_id === $clientId, 404);

        return view('dashboards.accounts.chart.form', [
            'account' => $account,
            'parents' => Account::forClient($clientId)
                ->where('is_group', true)
                ->whereKeyNot($account->id)
                ->orderBy('account_number')
                ->get(),
            'rootTypes' => Account::ROOT_TYPES,
            'accountTypes' => Account::ACCOUNT_TYPES,
            'hasEntries' => $account->lines()->exists() || $account->children()->exists(),
        ]);
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $this->allow('update accounts');
        $clientId = $this->clientId();
        abort_unless((int) $account->client_id === $clientId, 404);

        $data = $this->validated($request, $clientId, $account);
        $hasEntries = $account->lines()->exists();

        // Changing the nature of an account that already has postings would corrupt reports.
        if ($hasEntries) {
            $data['root_type'] = $account->root_type;
            $data['is_group'] = false;
        }

        if (! empty($data['parent_id'])) {
            $parent = Account::forClient($clientId)->findOrFail($data['parent_id']);
            abort_if($parent->id === $account->id, 422, 'An account cannot be its own parent.');
            $data['root_type'] = $parent->root_type;
        }

        $account->update($data);

        return redirect()
            ->route('accounts.chart.index')
            ->with('success', 'Account updated.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->allow('delete accounts');
        abort_unless((int) $account->client_id === $this->clientId(), 404);

        if ($account->lines()->exists()) {
            return back()->with('error', 'This account already has journal entries. Disable it instead of deleting it.');
        }

        if ($account->children()->exists()) {
            return back()->with('error', 'This account has sub-accounts. Move or delete them first.');
        }

        $account->delete();

        return back()->with('success', 'Account deleted.');
    }

    private function validated(Request $request, int $clientId, ?Account $account = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('accounts', 'name')
                    ->where('client_id', $clientId)
                    ->ignore($account?->id),
            ],
            'account_number' => ['nullable', 'string', 'max:30'],
            'parent_id' => ['nullable', 'integer'],
            'root_type' => ['required', Rule::in(Account::ROOT_TYPES)],
            'account_type' => ['nullable', Rule::in(Account::ACCOUNT_TYPES)],
            'currency' => ['nullable', 'string', 'max:10'],
            'opening_balance' => ['nullable', 'numeric'],
            'opening_date' => ['nullable', 'date'],
            'is_group' => ['nullable', 'boolean'],
            'disabled' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]) + [
            'is_group' => $request->boolean('is_group'),
            'disabled' => $request->boolean('disabled'),
            'currency' => $request->input('currency') ?: 'TZS',
            'opening_balance' => $request->input('opening_balance') ?: 0,
        ];
    }
}
