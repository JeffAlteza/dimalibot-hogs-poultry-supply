<?php

namespace App\Filament\Resources\FeedsResource\Pages;

use App\Filament\Resources\FeedsResource;
use App\Filament\Resources\FeedsResource\Widgets\FeedsOverview;
use App\Traits\RedirectToIndexTrait;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListFeeds extends ListRecords
{
    use RedirectToIndexTrait;

    protected static string $resource = FeedsResource::class;

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'UltraPack' => Tab::make()->query(fn ($query) => $query->where('type', 'UltraPack')),
            'Starter' => Tab::make()->query(fn ($query) => $query->where('type', 'Starter')),
            'Grower' => Tab::make()->query(fn ($query) => $query->where('type', 'Grower')),
            'Fattener' => Tab::make()->query(fn ($query) => $query->where('type', 'Fattener')),
            'Breeder' => Tab::make()->query(fn ($query) => $query->where('type', 'Breeder')),
        ];
    }
}
