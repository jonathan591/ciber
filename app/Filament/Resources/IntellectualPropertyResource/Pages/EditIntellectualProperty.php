<?php

namespace App\Filament\Resources\IntellectualPropertyResource\Pages;

use App\Filament\Resources\IntellectualPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Cloutier\PhpIpfsApi\IPFS;
class EditIntellectualProperty extends EditRecord
{
    protected static string $resource = IntellectualPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Obtiene el nombre del archivo
    $fileName = $data['file']; 

    // Define la ruta donde está almacenado el archivo
    $filePath = storage_path('app/public/' . $fileName);

    // Verifica si el archivo existe
    if (!file_exists($filePath)) {
        throw new \Exception("El archivo no se encuentra en la ruta especificada.");
    }

    // Inicializar la conexión con IPFS
    $ipfs = new IPFS("localhost", "8080", "5001");

    // Subir el archivo a IPFS
    try {
        // Cargar el archivo en IPFS
        $file = $ipfs->add($filePath, ['chunked' => true]); // Usar la ruta completa al archivo

        // Obtener el hash del archivo subido
        $data['file_hash'] =$file; // Guarda el hash en el array de datos
    } catch (\Exception $e) {
        throw new \Exception("Error al subir el archivo a IPFS: " . $e->getMessage());
    }

    // Devuelve los datos modificados
    return $data;
}
}
