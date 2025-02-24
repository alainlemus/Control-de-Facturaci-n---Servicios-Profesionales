<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\CatalogoRegimenFiscal;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('rfc')
                    ->label('RFC')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('rfc', strtoupper($state)))
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
                Forms\Components\TextInput::make('nombre_cliente')
                    ->label('Nombre del Cliente')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('nombre_cliente', strtoupper($state)))
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
                Forms\Components\Textarea::make('razon_social')
                    ->label('Razón Social')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('razon_social', strtoupper($state)))
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
                Forms\Components\Select::make('tipo_persona')
                ->label('Tipo de Persona')
                    ->options([
                        'FISICA' => 'Persona Física',
                        'MORAL' => 'Persona Moral',
                    ])
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
                Forms\Components\TextInput::make('codigo_postal')
                    ->required()
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
                Forms\Components\Select::make('catalogo_regimen_fiscal_id')
                    ->label('Régimen Fiscal')
                    ->options(function (callable $get) {
                        $tipoPersona = $get('tipo_persona');
                        if ($tipoPersona) {
                            return CatalogoRegimenFiscal::where('tipo_persona', $tipoPersona)
                                ->get()
                                ->mapWithKeys(function ($regimen) {
                                    return [$regimen->id => "{$regimen->num_regimen} - {$regimen->descripcion}"];
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'El :attribute es necesario.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rfc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('razon_social')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_persona'),
                Tables\Columns\TextColumn::make('tipo_persona'),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
