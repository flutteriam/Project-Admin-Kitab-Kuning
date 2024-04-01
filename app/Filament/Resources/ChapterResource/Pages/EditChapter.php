<?php

namespace App\Filament\Resources\ChapterResource\Pages;

use Filament\Actions;
use App\Models\Chapter;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ChapterResource;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->before(function ($record) {
                $nextChapters = Chapter::where('order', '>', $record->order)->get();
                foreach ($nextChapters as $chapter) {
                    $chapter->update(['order' => $chapter->order - 1]);
                }
            }),
        ];
    }
}
