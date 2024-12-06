<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntellectualPropertyResource\Pages;
use App\Filament\Resources\IntellectualPropertyResource\RelationManagers;
use App\Models\IntellectualProperty;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class IntellectualPropertyResource extends Resource
{
    protected static ?string $model = IntellectualProperty::class;

    protected static ?string $navigationIcon = 'heroicon-m-book-open';
    protected static ?string $modelLabel = 'Propiedad Intelectual';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Patente';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Verifica si el usuario tiene el rol de superadmin
        if (Auth::user()->hasRole('super_admin')) {
            return $query; // El superadmin puede ver todos los registros
        }

        // Para otros usuarios, filtra por el `user_id` del usuario logueado
        return $query->where('owner_id', Auth::user()->id);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->label("Propietario")
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->options(function () {
                        $user = Auth::user();

                        // Si el usuario es un superadmin, muestra todos los usuarios.
                        if ($user->hasRole('super_admin')) {
                            return User::all()->pluck('name', 'id');
                        }

                        // Si es otro rol, muestra solo el usuario logueado.
                        return User::where('id', $user->id)->pluck('name', 'id');
                    }),
                    
                Forms\Components\TextInput::make('name')
                    ->label("Nombre")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author')
                    ->label("Author")
                    ->required()
                    ->maxLength(255),
                    Forms\Components\Select::make('category_id')
                    ->label("Categoria")
                    ->relationship('category', 'name')
                    ->required()
                    ,
                Forms\Components\Textarea::make('description')
                    ->label("Descripcion")
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file')
                    ->label("Archivo")
                    ->required()

                    ->maxSize(1024 * 10) // Establecer un tamaño máximo de archivo (en kilobytes)
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/zip'])
                    ->label('Subir archivo'),
                // Forms\Components\TextInput::make('file_hash')
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label("Propietario")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label("Nombre")
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->label("Author")
                    ->searchable(),
                    Tables\Columns\TextColumn::make('category.name')
                    ->label("categoria")
                    ->searchable(),
                Tables\Columns\TextColumn::make('file')
                    ->label("Archivo")
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_hash')
                    ->label("Has Archivo")
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
            'index' => Pages\ListIntellectualProperties::route('/'),
            'create' => Pages\CreateIntellectualProperty::route('/create'),
            'edit' => Pages\EditIntellectualProperty::route('/{record}/edit'),
        ];
    }
}
