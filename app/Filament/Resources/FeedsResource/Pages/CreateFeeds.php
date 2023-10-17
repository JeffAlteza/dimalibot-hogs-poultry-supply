<?php

namespace App\Filament\Resources\FeedsResource\Pages;

use App\Filament\Resources\FeedsResource;
use App\Traits\RedirectToIndexTrait;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateFeeds extends CreateRecord
{
    use RedirectToIndexTrait;

    protected static string $resource = FeedsResource::class;
}
