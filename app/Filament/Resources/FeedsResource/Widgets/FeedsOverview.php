<?php

namespace App\Filament\Resources\FeedsResource\Widgets;

use App\Models\Feeds;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FeedsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $feeds = Feeds::all();

        $starter = $feeds->where('type', 'Starter')->sum('stocks');
        $grower = $feeds->where('type', 'Grower')->sum('stocks');
        $fattener = $feeds->where('type', 'Fattener')->sum('stocks');
        $breeder = $feeds->where('type', 'Breeders')->sum('stocks');

        return [
            Stat::make('Ultra Pack', $starter)
                ->description('Total Stocks')
                // ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Starter', $starter)
                ->description('Total Stocks')
                // ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Grower', $grower)
                ->description('Total Stocks')
                // ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Fattener', $fattener)
                ->description('Total Stocks')
                // ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Breeders', $breeder)
                ->description('Total Stocks')
                // ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),
        ];
    }
}
