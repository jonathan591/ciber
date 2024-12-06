<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Crypt;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {


        // Obtener el ticket
        $privare = $data['privarykey'];

        $data['privarykey'] = Crypt::encrypt($privare);


        return $data;
    }
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
