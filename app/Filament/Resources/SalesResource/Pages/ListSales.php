<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Filament\Resources\SalesResource\Widgets\SalesOverview;
// use App\Filament\Widgets\SalesOverview as WidgetsSalesOverview;
use App\Traits\RedirectToIndexTrait;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListSales extends ListRecords
{
    use RedirectToIndexTrait;

    protected static string $resource = SalesResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         ExportAction::make('export')->color('success'),
    //         CreateAction::make()->icon('heroicon-o-document-plus'),
    //     ];
    // }

    protected function getHeaderWidgets():array
    {
        return[
            SalesOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Paid' => Tab::make()->query(fn ($query) => $query->where('status', 'Paid')),
            'Insufficient' => Tab::make()->query(fn ($query) => $query->where('status', 'Insufficient')),
            'Unpaid' => Tab::make()->query(fn ($query) => $query->where('status', 'Unpaid')),
        ];
    }
}
