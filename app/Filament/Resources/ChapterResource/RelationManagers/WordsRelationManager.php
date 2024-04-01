<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use Filament\Forms;
use App\Models\Word;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class WordsRelationManager extends RelationManager
{
    protected static string $relationship = 'words';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('arab')
                    ->required()
                    ->maxLength(255),
                TextInput::make('arab_harokat')
                    ->required()
                    ->maxLength(255),
                TextInput::make('translate')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('arab')
            ->columns([
                TextColumn::make('order'),
                TextColumn::make('arab'),
                TextColumn::make('arab_harokat'),
                TextColumn::make('translate'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $count = Word::whereChapterId($this->getOwnerRecord()->id)->count() + 1;
                        $data['book_id'] = $this->getOwnerRecord()->book->id;
                        $data['bab_id'] = $this->getOwnerRecord()->bab->id;
                        $data['basic'] = '';
                        $data['order'] = $count;
                        return $data;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        $nextWords = Word::where('order', '>', $record->order)->get();
                        foreach ($nextWords as $word) {
                            $word->update(['order' => $word->order - 1]);
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

    public function isReadOnly(): bool
    {
        return false;
    }
}
