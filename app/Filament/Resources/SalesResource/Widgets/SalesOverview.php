<?php

namespace App\Filament\Resources\SalesResource\Widgets;

use App\Models\Sales;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $sales = Sales::all();

        $totalSales = $sales->sum('total');

        $paidAmount = $sales->sum('paid_price');

        $paidAmountThisMonth = Sales::whereMonth('updated_at', Carbon::now()->month)->sum('paid_price');

        $insufficientCollection = $sales->where('status', 'Insufficient')->map(function ($sale) {
            return ($sale['total'] - $sale['paid_price']);
        });

        $insufficient = $insufficientCollection->sum();

        $unpaidAmount = $sales->where('status', 'Unpaid')->sum('total');

        return [
            Stat::make('Total', '₱' . number_format($totalSales, 2))
                ->description('Total Sales')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),
            Stat::make('Paid', '₱' . number_format($paidAmount, 2))
                ->description('Total Paid Amount')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Paid this ' . Carbon::now()->format('F'), '₱' . number_format($paidAmountThisMonth, 2))
                ->description('Total Paid Amount This Month')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Unpaid & Insufficient', '₱' . number_format($unpaidAmount + $insufficient, 2))
                ->description('Total Unpaid Amount')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
