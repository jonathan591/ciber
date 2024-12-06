<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';
    protected static ?string $modelLabel = 'Usuarios';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Usuario';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label("Nombre")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label("Correo")
                    ->email()
                    ->required()
                    ->maxLength(255),
                // Forms\Components\DateTimePicker::make('email_verified_at')
                //     ->label("Correo verificado"),
                Forms\Components\TextInput::make('password')
                    ->label("Contraseña")
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('from_adrress')
                    ->maxLength(300),
                Forms\Components\TextInput::make('privarykey')
                    ->maxLength(1000)
                    ->hiddenOn('edit'),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Nombre")
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label("Correo")
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label("Correo verificado")
                    ->dateTime()
                    ->sortable(),
                   Tables\Columns\TextColumn::make('roles.name')->label('Role')
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->colors(['info'])
                    ->badge(),
                Tables\Columns\TextColumn::make('from_adrress')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('privarykey')
                //     ->searchable(),
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
                Tables\Filters\Filter::make('verified')
                ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')->color('success')
                ->icon('heroicon-m-check-badge')->action(
                    function(User $user){
                        $user->email_verified_at= Date('Y-m-d H:m:s');
                        $user->save();
                    }
                ),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
