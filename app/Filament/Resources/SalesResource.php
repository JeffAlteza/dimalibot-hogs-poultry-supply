<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Filament\Resources\SalesResource\Widgets\SalesOverview;
use App\Models\Customer;
use App\Models\Feeds;
use App\Models\Sales;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction as ActionsCreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationGroup = 'Sales Management';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Fieldset::make('Details')->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(
                                function (callable $set, ?string $state) {
                                    $customer = Customer::find($state);
                                    $set('customer_name', $customer->name ?? '');
                                }
                            )
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('address'),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create customer')
                                    ->modalWidth('lg');
                            })
                            ->native(false)
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\Hidden::make('customer_name'),
                        Forms\Components\Select::make('feeds_id')
                            ->label('Feeds')
                            ->options(Feeds::where('stocks', '>', 0)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(
                                function (callable $set, ?string $state) {
                                    $feeds = Feeds::find($state);
                                    $set('feeds_name', $feeds->name);
                                    $set('feeds_type', $feeds->type);
                                    $set('feeds_bought_price', $feeds->bought_price);
                                    $set('feeds_price', $feeds->selling_price);
                                    $set('stocks', $feeds->stocks);
                                }
                            )
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->minValue(1)
                            ->maxValue(fn (Get $get): int => Feeds::find($get('feeds_id'))?->stocks ?? 0)
                            ->live()
                            ->afterStateUpdated(
                                function (callable $set, callable $get, ?int $state) {
                                    $set('total', $get('feeds_price') * $state);
                                    $set('profit', ($get('feeds_price') - $get('feeds_bought_price')) * $state);
                                }
                            )
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Paid' => 'Paid',
                                'Unpaid' => 'Unpaid',
                                'Insufficient' => 'Insufficient',
                            ])
                            ->live()
                            ->afterStateUpdated(
                                function (callable $set, callable $get, ?string $state) {
                                    if ($state == 'Unpaid') {
                                        $set('paid_price', 0);
                                    } else {
                                        $set('paid_price', $get('feeds_price') * $get('quantity'));
                                    }
                                }
                            )
                            ->native(false)
                            ->reactive()
                            ->required(),
                        Forms\Components\DatePicker::make('updated_at')
                            ->label('Date')
                            ->default(Carbon::now())
                            ->required(),
                        Forms\Components\TextInput::make('paid_price')
                            ->disabled(fn (Get $get) => $get('status') != 'Insufficient')
                            ->dehydrated()
                            ->numeric()
                            ->prefix('₱'),
                        Forms\Components\TextInput::make('total')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->numeric()
                            ->prefix('₱'),
                        Forms\Components\Hidden::make('feeds_bought_price')->disabled(),
                        Forms\Components\Hidden::make('profit'),
                    ]),
                ])->columns(2)
                    ->columnSpan(2),

                Section::make()->schema([
                    Fieldset::make('Feeds Details')->schema([
                        Forms\Components\TextInput::make('feeds_name')
                            ->label('Name')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('feeds_type')
                            ->label('Type')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('stocks')
                            ->disabled(),
                        Forms\Components\TextInput::make('feeds_price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('₱')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(1)
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Order Date')
                    ->date(),
                Tables\Columns\TextColumn::make('customer_name'),
                Tables\Columns\TextColumn::make('feeds_name'),
                Tables\Columns\TextColumn::make('feeds_type'),
                Tables\Columns\TextColumn::make('feeds_price')
                    ->prefix('₱')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total')
                    ->prefix('₱')
                    ->numeric(2),
                // ->summarize([
                //     Tables\Columns\Summarizers\Sum::make()->money('PHP')
                // ]),
                Tables\Columns\TextColumn::make('paid_price')
                    ->prefix('₱')
                    ->numeric(2),
                // ->summarize([
                //     Tables\Columns\Summarizers\Sum::make()->money('PHP')
                // ]),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'danger' => 'Unpaid',
                        'success' => 'Paid',
                        'primary' => 'Insufficient',
                    ])
                    ->alignCenter()
                    ->badge(),
                Tables\Columns\TextColumn::make('profit')
                    ->toggledHiddenByDefault()
                    ->prefix('₱')
                    ->numeric(2),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('success'),
                    Tables\Actions\EditAction::make()->color('primary'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal')
            ])
            ->headerActions([
                ExportAction::make('export')->exports([
                    ExcelExport::make('form')
                        ->askForFilename()
                        ->withFilename(fn ($filename) => $filename . ':' . date('M-d-Y'))
                        ->fromTable()
                ])->color('success'),
                ActionsCreateAction::make()->icon('heroicon-o-document-plus'),
            ])
            ->bulkActions([
                ExportBulkAction::make('export'),
                DeleteBulkAction::make('delete'),
                RestoreBulkAction::make('restore')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            SalesOverview::class
        ];
    }
}
