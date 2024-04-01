<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use App\Models\Bab;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BabsRelationManager extends RelationManager
{
    protected static string $relationship = 'babs';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $count = Bab::count() + 1;
                        $data['order'] = $count;

                        return $data;
                    }),
            ])
            ->actions([
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
}
