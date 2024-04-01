<?php

namespace App\Filament\Resources\BabResource\Pages;

use App\Models\Bab;
use Filament\Actions;
use App\Filament\Resources\BabResource;
use Filament\Resources\Pages\EditRecord;

class EditBab extends EditRecord
{
    protected static string $resource = BabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record) {
                $nextBabs = Bab::where('order', '>', $record->order)->get();
                foreach ($nextBabs as $bab) {
                    $bab->update(['order' => $bab->order - 1]);
                }
            }),
        ];
    }
}
