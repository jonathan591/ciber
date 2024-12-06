<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\IntellectualProperty;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Web3\Contract;
use Web3\Utils;
use Web3\Web3;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Crypt;
use Web3p\EthereumTx\Transaction;

class CreateTransaction extends CreateRecord
{
  protected static string $resource = TransactionResource::class;
  protected $transactionHash;
  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $propiedad_id = $data['intellectual_property_id'];
    $intelectual = IntellectualProperty::find($propiedad_id);
    $name = $intelectual->name;
    $autor = $intelectual->author;
    $description = $intelectual->description;
    $has = $intelectual->file_hash;
    $propietaria = $intelectual->owner_id;

    $uswer = User::find($propietaria);
    $direccion = $uswer->from_adrress;
    $claveopriva = $uswer->privarykey;

    $clavedescri = Crypt::decrypt($claveopriva);


    $userid = User::find($data['user_id']);
    $address = $userid->from_adrress;

    $infuraUrl = "http://127.0.0.1:8545";
    $web3 = new Web3($infuraUrl);
    $contractAddress = Utils::toChecksumAddress($address);
    $contractAbi = '[
    {
      "inputs": [
        {
          "internalType": "address",
          "name": "",
          "type": "address"
        }
      ],
      "name": "files",
      "outputs": [
        {
          "internalType": "string",
          "name": "name",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "description",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "author",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "hash",
          "type": "string"
        }
      ],
      "stateMutability": "view",
      "type": "function",
      "constant": true
    },
    {
      "inputs": [
        {
          "internalType": "string",
          "name": "_name",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "_description",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "_author",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "_hash",
          "type": "string"
        }
      ],
      "name": "storeFile",
      "outputs": [],
      "stateMutability": "nonpayable",
      "type": "function"
    },
    {
      "inputs": [],
      "name": "getFile",
      "outputs": [
        {
          "internalType": "string",
          "name": "",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "",
          "type": "string"
        },
        {
          "internalType": "string",
          "name": "",
          "type": "string"
        }
      ],
      "stateMutability": "view",
      "type": "function",
      "constant": true
    }
  ]';

    // Instancia del contrato
    $contract = new Contract($web3->provider, $contractAbi);

    // Clave privada (almacénala en un entorno seguro)
    $privateKey = $clavedescri;
    $fromAddress = $direccion;


    $data1 = $contract->at($contractAddress)->getData('storeFile', $name, $description, $autor, $has);
    // Obtener nonce para la transacción

    $web3->eth->getTransactionCount($fromAddress, function ($err, $nonce) use ($web3, $contractAddress, $data1, $fromAddress, $privateKey) {
      if ($err !== null) {
        echo "Error al obtener el nonce: " . $err->getMessage();
        return;
      }

      // Asegúrate de convertir el nonce a un valor que dechex pueda manejar
      $nonceHex = '0x' . dechex((int) $nonce->toString());

      // Preparar datos de la función storeFile codificados

      // Crear la transacción
      $tx = [
        'nonce' => $nonceHex,
        'from' => $fromAddress,
        'to' => $contractAddress,
        'gas' => Utils::toHex(2000000, true),
        'gasPrice' => Utils::toHex(Utils::toWei('20', 'gwei'), true),
        'data' => $data1,
        'chainId' => 1337 // ID de red local de Ganache
      ];

      // Firmar la transacción
      $transaction = new Transaction($tx);
      $signedTx = '0x' . $transaction->sign($privateKey);



      // Enviar la transacción firmada
      $web3->eth->sendRawTransaction($signedTx, function ($err, $txHash) {
        if ($err !== null) {
          echo 'Error al enviar la transacción: ' . $err->getMessage();
          return;
        }


        //   echo "Transacción enviada con éxito. Hash: $txHash\n";
        Notification::make()
          ->title('Transacción')
          ->body('Transacción enviada con éxito. Hash.' . $txHash)
          ->success()
          ->send();
        // Asigna $txHash a la propiedad de clase
        $this->transactionHash = $txHash;
      });
    });
    //   $data['transaction_hash']=$txHash;

    $data['transaction_hash'] = $this->transactionHash;
    $data['from_address'] = $fromAddress;
    $data['to_address'] = $contractAddress;

    return $data;
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
