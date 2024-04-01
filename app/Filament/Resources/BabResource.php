<?php

namespace App\Filament\Resources;

use App\Models\Bab;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\BabResource\Pages;

class BabResource extends Resource
{
    protected static ?string $model = Bab::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('translate_title')
                    ->required()
                    ->maxLength(255),
            ]);
    }



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    TextEntry::make('book.category.name')
                        ->label('Category')
                        ->columns(6),
                    TextEntry::make('book.title')
                        ->label('Kitab')
                        ->columns(6),
                    TextEntry::make('title')
                        ->columns(6),
                    TextEntry::make('translate_title')
                        ->columns(6),
                ])
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('order'),
                TextColumn::make('title'),
                TextColumn::make('translate_title'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Model $record): string => BabResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record) {
                    $nextBabs = Bab::where('order', '>', $record->order)->get();
                    foreach ($nextBabs as $cat) {
                        $cat->update(['order' => $cat->order - 1]);
                    }
                }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
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
            'index' => Pages\ListBabs::route('/'),
            'create' => Pages\CreateBab::route('/create'),
            'view' => Pages\ViewBab::route('/{record}'),
            'edit' => Pages\EditBab::route('/{record}/edit'),
        ];
    }
}
