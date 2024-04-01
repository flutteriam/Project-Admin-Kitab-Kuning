<?php

namespace App\Filament\Resources\BabResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Chapter;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\CreateAction;
use App\Filament\Resources\ChapterResource;
use Filament\Resources\RelationManagers\RelationManager;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    public function form(Form $form): Form
    {
        return ChapterResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ChapterResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $count = Chapter::whereBabId($this->getOwnerRecord()->id)->count() + 1;
                        $data['book_id'] = $this->getOwnerRecord()->book->id;
                        $data['order'] = $count;
                        return $data;
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
