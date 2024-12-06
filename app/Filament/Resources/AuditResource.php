<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AuditExporter;
use App\Filament\Resources\AuditResource\Pages;
use App\Filament\Resources\AuditResource\RelationManagers;
use App\Models\Audit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-s-queue-list';
    protected static ?string $modelLabel = 'Auditoria';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Autoria';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_type')
                ->label("usuario nodel")
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                ->label("Usuario")
                ->required()
                ->relationship('user', 'name'),
                Forms\Components\TextInput::make('event')
                ->label("Evento")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('auditable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('auditable_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('old_values')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('new_values')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(45),
                Forms\Components\TextInput::make('user_agent')
                    ->maxLength(1023),
                Forms\Components\TextInput::make('tags')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                ->label("Usuario")
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                ->label("Evento")
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'deleted' => 'danger', // Rojo
                        'updated' => 'warning', // Naranja
                        'created' => 'success', // Verde
                        default => 'secondary', // Gris para otros
                    }),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('tags')
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
                ExportBulkAction::make()
                ->color("success")
                ->after(function () {
                    Notification::make()
                        ->title('Excel')
                        ->body('Excel de auditoria exportado exitosamente.')
                        ->success()
                        ->send();
                }),
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
            'index' => Pages\ListAudits::route('/'),
            'create' => Pages\CreateAudit::route('/create'),
            'edit' => Pages\EditAudit::route('/{record}/edit'),
        ];
    }
}
