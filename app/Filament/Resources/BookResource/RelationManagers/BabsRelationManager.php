<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use App\Models\Bab;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\BabResource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BabsRelationManager extends RelationManager
{
    protected static string $relationship = 'babs';

    public function form(Form $form): Form
    {
        return BabResource::form($form);
    }

    public function table(Table $table): Table
    {
        return BabResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $count = Bab::whereBookId($this->getOwnerRecord()->id)->count() + 1;
                        $data['order'] = $count;
                        return $data;
                    }),
            ]);
    }
}
