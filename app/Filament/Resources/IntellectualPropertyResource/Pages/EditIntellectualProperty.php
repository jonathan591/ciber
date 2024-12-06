<?php

namespace App\Filament\Resources\IntellectualPropertyResource\Pages;

use App\Filament\Resources\IntellectualPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntellectualProperty extends EditRecord
{
    protected static string $resource = IntellectualPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
