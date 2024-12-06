<?php

namespace App\Filament\Resources\IntellectualPropertyResource\Pages;

use App\Filament\Resources\IntellectualPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntellectualProperties extends ListRecords
{
    protected static string $resource = IntellectualPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
