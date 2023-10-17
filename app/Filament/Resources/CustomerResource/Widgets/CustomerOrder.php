<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CustomerOrder extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
            )
            ->columns([
                TextColumn::make('name'),
            ]);
    }
}
