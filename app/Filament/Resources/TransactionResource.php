<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\IntellectualProperty;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-m-arrow-path';
    protected static ?string $modelLabel = 'Transacion';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Transacion';
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Verifica si el usuario tiene el rol de superadmin
        if (Auth::user()->hasRole('super_admin')) {
            return $query; // El superadmin puede ver todos los registros
        }

        // Para otros usuarios, filtra por el `user_id` del usuario logueado
        return $query->where('user_id', Auth::user()->id);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('intellectual_property_id')
                ->label("Propiedad Intelectual")
                   ->relationship('propiedad', 'name')
                   ->searchable()
                   ->preload()
                   ->required()
                   ->options(function () {
                       $user = Auth::user();

                       // Si el usuario es un superadmin, muestra todos los usuarios.
                       if ($user->hasRole('super_admin')) {
                           return IntellectualProperty::all()->pluck('name', 'id');
                       }

                       // Si es otro rol, muestra solo el usuario logueado.
                       return IntellectualProperty::where('owner_id', $user->id)->pluck('name', 'id');
                   })
                  ,
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name')
                    ->label("Usuario")
                    ->searchable()
                    ->preload()
                   
                    ->options(function () {
                        $user = Auth::user();

                        // Si el usuario es un superadmin, muestra todos los usuarios.
                        if ($user->hasRole('super_admin')) {
                            return User::all()->pluck('name', 'id');
                        }

                        // Si es otro rol, muestra solo el usuario logueado.
                        return User::where('id', $user->id)->pluck('name', 'id');
                    })
                    ,
                // Forms\Components\TextInput::make('transaction_hash')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('from_address')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('to_address')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('propiedad.name')
                ->label("Propiedad Intelectual")
                  
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                ->label("Usuario")
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_hash')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
