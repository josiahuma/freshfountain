<?php

namespace App\Filament\Widgets;

use App\Support\Access\BackendAccess;

use App\Filament\Resources\Donations\DonationResource;
use App\Filament\Resources\FinanceTransactions\FinanceTransactionResource;
use App\Models\Donation;
use App\Models\FinanceTransaction;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceAnalyticsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading =
        'Finance Analytics';

    protected ?string $description =
        'Live income, expenditure, giving and Gift Aid statistics.';

    protected ?string $pollingInterval =
        '30s';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $today = CarbonImmutable::today();
        $monthStart = $today->startOfMonth();
        $monthEnd = $today->endOfMonth();
        $yearStart = $today->startOfYear();
        $yearEnd = $today->endOfYear();

        $incomeToday = $this->sumTransactions(
            FinanceTransaction::TYPE_INCOME,
            $today,
            $today
        );

        $incomeThisMonth = $this->sumTransactions(
            FinanceTransaction::TYPE_INCOME,
            $monthStart,
            $monthEnd
        );

        $expensesThisMonth = $this->sumTransactions(
            FinanceTransaction::TYPE_EXPENSE,
            $monthStart,
            $monthEnd
        );

        $netThisMonth =
            $incomeThisMonth - $expensesThisMonth;

        $incomeThisYear = $this->sumTransactions(
            FinanceTransaction::TYPE_INCOME,
            $yearStart,
            $yearEnd
        );

        $expensesThisYear = $this->sumTransactions(
            FinanceTransaction::TYPE_EXPENSE,
            $yearStart,
            $yearEnd
        );

        $giftAidEligible = Donation::query()
            ->where('status', Donation::STATUS_PAID)
            ->where('gift_aid', true)
            ->sum('amount');

        $giftAidEstimate =
            round((float) $giftAidEligible * 0.25, 2);

        $paidDonations = Donation::query()
            ->where('status', Donation::STATUS_PAID)
            ->count();

        $averageDonation = Donation::query()
            ->where('status', Donation::STATUS_PAID)
            ->avg('amount');

        $transactionsUrl =
            FinanceTransactionResource::getUrl('index');

        $donationsUrl =
            DonationResource::getUrl('index');

        return [
            Stat::make(
                'Income today',
                $this->money($incomeToday)
            )
                ->description('Completed income recorded today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url($transactionsUrl),

            Stat::make(
                'Income this month',
                $this->money($incomeThisMonth)
            )
                ->description(
                    $this->money($incomeThisYear)
                    . ' income this year'
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->url($transactionsUrl),

            Stat::make(
                'Expenses this month',
                $this->money($expensesThisMonth)
            )
                ->description(
                    $this->money($expensesThisYear)
                    . ' expenses this year'
                )
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->url($transactionsUrl),

            Stat::make(
                'Net this month',
                $this->money($netThisMonth)
            )
                ->description(
                    $netThisMonth >= 0
                        ? 'Positive monthly position'
                        : 'Expenses exceed income'
                )
                ->descriptionIcon(
                    $netThisMonth >= 0
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-exclamation-triangle'
                )
                ->color(
                    $netThisMonth >= 0
                        ? 'success'
                        : 'danger'
                )
                ->url($transactionsUrl),

            Stat::make(
                'Paid donations',
                number_format($paidDonations)
            )
                ->description(
                    'Average gift: '
                    . $this->money(
                        (float) ($averageDonation ?? 0)
                    )
                )
                ->descriptionIcon('heroicon-m-heart')
                ->color('info')
                ->url($donationsUrl),

            Stat::make(
                'Gift Aid eligible',
                $this->money((float) $giftAidEligible)
            )
                ->description(
                    'Estimated reclaim: '
                    . $this->money($giftAidEstimate)
                )
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning')
                ->url($donationsUrl),
        ];
    }

    private function sumTransactions(
        string $type,
        CarbonImmutable $from,
        CarbonImmutable $to
    ): float {
        return (float) FinanceTransaction::query()
            ->where('type', $type)
            ->where(
                'status',
                FinanceTransaction::STATUS_COMPLETED
            )
            ->whereBetween(
                'transaction_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->sum('amount');
    }

    private function money(float $amount): string
    {
        return '£' . number_format($amount, 2);
    }

    public static function canView(): bool
    {
        return BackendAccess::canView('finance');
    }
}
