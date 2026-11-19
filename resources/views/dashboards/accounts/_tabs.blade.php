@php
    $tabs = [
        ['route' => 'accounts.dashboard', 'label' => 'Overview'],
        ['route' => 'accounts.chart.index', 'label' => 'Chart of Accounts'],
        ['route' => 'accounts.journal.index', 'label' => 'Journal Entries'],
        ['route' => 'accounts.ledger.index', 'label' => 'General Ledger'],
        ['route' => 'accounts.reports.trial_balance', 'label' => 'Trial Balance'],
        ['route' => 'accounts.reports.profit_loss', 'label' => 'Profit & Loss'],
        ['route' => 'accounts.reports.balance_sheet', 'label' => 'Balance Sheet'],
        ['route' => 'accounts.reports.receivables', 'label' => 'Receivables'],
        ['route' => 'accounts.reports.cash_bank', 'label' => 'Cash & Bank'],
        ['route' => 'accounts.settings', 'label' => 'Settings'],
    ];
@endphp
<div class="mb-4 d-flex flex-wrap gap-2">
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="btn btn-sm {{ request()->routeIs($tab['route']) ? 'btn-primary' : 'btn-outline-primary' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
