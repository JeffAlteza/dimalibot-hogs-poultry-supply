<?php

namespace App\Filament\Widgets;

use App\Models\Sales;
use Filament\Widgets\ChartWidget;

class SalesLineChart extends ChartWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?string $heading = 'Sales Report Chart';
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '275px';

    protected function getData(): array
    {
        $monthlySums = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlySums[] = Sales::whereMonth('updated_at', $month)->sum('paid_price');
        }

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return [
            'datasets' => [
                [
                    'label' => 'Sales Report',
                    'data' => $monthlySums,
                    'fill' => 'start',
                ],
            ],
            'labels' => $monthLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
