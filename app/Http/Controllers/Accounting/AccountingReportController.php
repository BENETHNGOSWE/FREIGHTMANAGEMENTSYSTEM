<?php

namespace App\Http\Controllers\Accounting;

use App\Services\Accounting\AccountingReports;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingReportController extends AccountingController
{
    public function trialBalance(Request $request, AccountingReports $reports): View
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $from = $request->get('from') ?: now()->startOfYear()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        return view('dashboards.accounts.reports.trial_balance', [
            'report' => $reports->trialBalance($clientId, $from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function profitLoss(Request $request, AccountingReports $reports): View
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $from = $request->get('from') ?: $reports->fiscalYearStart($clientId, now()->toDateString());
        $to = $request->get('to') ?: now()->toDateString();

        return view('dashboards.accounts.reports.profit_loss', [
            'report' => $reports->profitLoss($clientId, $from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function balanceSheet(Request $request, AccountingReports $reports): View
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $asOf = $request->get('as_of') ?: now()->toDateString();

        return view('dashboards.accounts.reports.balance_sheet', [
            'report' => $reports->balanceSheet($clientId, $asOf),
            'asOf' => $asOf,
        ]);
    }

    public function receivables(Request $request, AccountingReports $reports): View
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $asOf = $request->get('as_of') ?: now()->toDateString();

        return view('dashboards.accounts.reports.receivables', [
            'report' => $reports->receivables($clientId, $asOf),
            'asOf' => $asOf,
        ]);
    }

    public function cashBank(Request $request, AccountingReports $reports): View
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        return view('dashboards.accounts.reports.cash_bank', [
            'report' => $reports->cashAndBank($clientId, $from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    /** CSV download of the report currently on screen. */
    public function export(Request $request, AccountingReports $reports, string $report): StreamedResponse
    {
        $this->allow('view accounting reports');
        $clientId = $this->clientId();
        $to = $request->get('to') ?: $request->get('as_of') ?: now()->toDateString();
        $from = $request->get('from') ?: now()->startOfYear()->toDateString();

        $filename = 'accounts-' . $report . '-' . $to . '.csv';

        return response()->streamDownload(function () use ($report, $reports, $clientId, $from, $to) {
            $out = fopen('php://output', 'w');

            if ($report === 'trial-balance') {
                $data = $reports->trialBalance($clientId, $from, $to);
                fputcsv($out, ['Account', 'Type', 'Opening', 'Debit', 'Credit', 'Closing']);
                foreach ($data['rows'] as $row) {
                    fputcsv($out, [
                        $row['account']->account_number . ' ' . $row['account']->name,
                        $row['account']->root_type,
                        $row['opening'], $row['debit'], $row['credit'], $row['closing'],
                    ]);
                }
                fputcsv($out, ['TOTAL', '', '', $data['totals']['debit'], $data['totals']['credit'], '']);
            } elseif ($report === 'profit-loss') {
                $data = $reports->profitLoss($clientId, $from, $to);
                fputcsv($out, ['Section', 'Account', 'Amount']);
                foreach ($data['income'] as $row) {
                    fputcsv($out, ['Income', $row['account']->name, $row['amount']]);
                }
                foreach ($data['expenses'] as $row) {
                    fputcsv($out, ['Expense', $row['account']->name, $row['amount']]);
                }
                fputcsv($out, ['TOTAL INCOME', '', $data['total_income']]);
                fputcsv($out, ['TOTAL EXPENSE', '', $data['total_expense']]);
                fputcsv($out, ['NET PROFIT', '', $data['net_profit']]);
            } else {
                $data = $reports->receivables($clientId, $to);
                fputcsv($out, ['Customer', 'Invoices', 'Invoiced', 'Received', 'Balance', '0-30', '31-60', '61-90', '90+']);
                foreach ($data['customers'] as $row) {
                    fputcsv($out, [
                        $row['name'], $row['invoices'], $row['invoiced'], $row['received'], $row['balance'],
                        $row['current'], $row['days_31_60'], $row['days_61_90'], $row['over_90'],
                    ]);
                }
                fputcsv($out, ['TOTAL', '', $data['totals']['invoiced'], $data['totals']['received'], $data['totals']['balance'], '', '', '', '']);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
