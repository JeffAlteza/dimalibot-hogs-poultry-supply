<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Models\Feeds;
use App\Traits\RedirectToIndexTrait;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSales extends CreateRecord
{
    use RedirectToIndexTrait;

    protected static string $resource = SalesResource::class;

    protected function afterCreate(): void
    {
        $sales = $this->record;

        $feeds = Feeds::where('id', $sales->feeds_id)->first();

        if ($feeds) {

            $newStocks = $feeds->stocks - $sales->quantity;

            $feeds->update(['stocks' => $newStocks]);
        }
    }
}
