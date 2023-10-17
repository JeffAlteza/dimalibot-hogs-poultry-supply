<?php

namespace App\Filament\Widgets;

use App\Models\Sales;
use Filament\Widgets\ChartWidget;

class StatusChart extends ChartWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Status Chart';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '275px';

    protected function getData(): array
    {   
        $paid = Sales::where('status', 'Paid')->count();
        $unpaid = Sales::where('status', 'Unpaid')->count();
        $insufficient = Sales::where('status', 'Insufficient')->count();
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' =>[$paid, $unpaid, $insufficient],
                    'backgroundColor' => [
                        'rgba(54, 235, 127, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(54, 235, 127, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Paid', 'Unpaid', 'Insufficient'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
