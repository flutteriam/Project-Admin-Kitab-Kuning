<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use App\Models\Category;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategoryResource;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record) {
                $nextCategory = Category::where('order', '>', $record->order)->get();
                foreach ($nextCategory as $cat) {
                    $cat->update(['order' => $cat->order - 1]);
                }
            }),
        ];
    }
}
