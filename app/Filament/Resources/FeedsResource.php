<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedsResource\Pages;
use App\Filament\Resources\FeedsResource\RelationManagers;
use App\Filament\Resources\FeedsResource\Widgets\FeedsOverview;
use App\Models\Feeds;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction as ActionsCreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as TablesExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class FeedsResource extends Resource
{
    protected static ?string $model = Feeds::class;

    protected static ?string $navigationGroup = 'Sales Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Fieldset::make('Details')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Group::make()->schema([
                            Forms\Components\TextInput::make('stocks')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('bought_price')
                                ->required()
                                ->numeric()
                                ->prefix('₱'),
                            Forms\Components\TextInput::make('selling_price')
                                ->required()
                                ->numeric()
                                ->prefix('₱'),
                        ])->columns(3)
                    ])->columns(1)
                ])->columnSpan(2),
                Forms\Components\Section::make()->schema([
                    Fieldset::make('Type')->schema([
                        Forms\Components\Radio::make('type')
                            ->options([
                                'UltraPack' => 'UltraPack',
                                'Starter' => 'Starter',
                                'Grower' => 'Grower',
                                'Fattener' => 'Fattener',
                                'Breeder' => 'Breeder',
                            ])
                            // ->native(false)
                            ->required(),
                    ])
                ])
                    ->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('stocks')
                    ->badge()
                    ->numeric(),
                Tables\Columns\TextColumn::make('bought_price')
                    ->currency('PHP'),
                Tables\Columns\TextColumn::make('selling_price')
                    ->currency('PHP'),
                Tables\Columns\TextColumn::make('updated_at')->date()
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
                TablesExportAction::make('export')->exports([
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
            'index' => Pages\ListFeeds::route('/'),
            'create' => Pages\CreateFeeds::route('/create'),
            'edit' => Pages\EditFeeds::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            FeedsOverview::class
        ];
    }
}
