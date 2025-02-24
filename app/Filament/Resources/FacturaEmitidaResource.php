<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaEmitidaResource\Pages;
use App\Models\CatalogoRegimenFiscal;
use App\Models\Cliente;
use App\Models\FacturaEmitida;
use Filament\Actions\StaticAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
//use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Illuminate\Database\Eloquent\Collection;

class FacturaEmitidaResource extends Resource
{
    protected static ?string $model = FacturaEmitida::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('cliente_id')
            ->label('Seleccionar Cliente')
            ->options(Cliente::all()->pluck('nombre_cliente', 'id'))
            ->searchable()
            ->required()
            ->createOptionForm([
                TextInput::make('rfc')->required()->label('RFC'),
                TextInput::make('nombre_cliente')->required()->label('Nombre Cliente'),
                TextInput::make('razon_social')->required()->label('Razón Social'),
                TextInput::make('codigo_postal')->required()->label('Código Postal'),
                Select::make('tipo_persona')
                    ->options([
                        'FISICA' => 'Persona Física',
                        'MORAL' => 'Persona Moral',
                    ])
                    ->label('Tipo de Persona')
                    ->required(),
                Select::make('catalogo_regimen_fiscal_id')
                    ->label('Régimen Fiscal')
                    ->options(CatalogoRegimenFiscal::all()->mapWithKeys(function ($regimen) {
                        return [$regimen->id => "{$regimen->num_regimen} - {$regimen->descripcion}"];
                    }))
                    ->searchable()
                    ->required(),
            ])
            ->validationMessages([
                'required' => 'El :attribute es necesario.',
            ]),
        FileUpload::make('archivos')
            ->label('Cargar PDF(s)')
            ->multiple()
            ->disk('local')
            ->directory('public/uploads/facturas_emitidas')
            ->acceptedFileTypes(['application/pdf'])
            ->maxFiles(100)
            ->required()
            ->validationMessages([
                'required' => 'Por favor, carga al menos un archivo PDF.',
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('rfc_emisor')->label('RFC Emisor'),
            Tables\Columns\TextColumn::make('nombre_emisor')
                ->label('Emisor')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('subtotal')
                ->label('Subtotal')
                ->sortable()
                ->money('mxn'),
            Tables\Columns\TextColumn::make('iva_trasladado')
                ->label('IVA trasladado')
                ->sortable()
                ->money('mxn'),
            Tables\Columns\TextColumn::make('iva_retenido')
                ->label('IVA retenido')
                ->sortable()
                ->money('mxn'),
            Tables\Columns\TextColumn::make('isr_retenido')
                ->label('ISR retenido')
                ->sortable()
                ->money('mxn'),
            Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->searchable()
                ->sortable()
                ->money('mxn'),
            Tables\Columns\TextColumn::make('fecha_emision')
                ->label('Fecha de emisión')
                ->searchable()
                ->sortable()
                ->dateTime('d/m/Y H:i'), // Formato personalizado de fecha y hora
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de creación')
                ->sortable()
                ->dateTime('d/m/Y H:i')
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->bulkActions([
            BulkAction::make('delete_selected')
                ->label('Eliminar seleccionados')
                ->color('danger')
                ->requiresConfirmation() // Solicitar confirmación antes de ejecutar la acción
                ->action(function (Collection $records) {
                    foreach ($records as $record) {
                        // Eliminar el archivo PDF asociado
                        if ($record->pdf_filename && file_exists(storage_path('app/public/uploads/facturas_emitidas/' . $record->pdf_filename))) {
                            unlink(storage_path('app/public/uploads/facturas_emitidas/' . $record->pdf_filename));
                        }

                        // Eliminar el registro
                        $record->delete();
                    }

                    Notification::make()
                        ->title('Éxito')
                        ->body('Registros eliminados correctamente.')
                        ->success()
                        ->send();
                }),
        ])
        ->filters([

            // Filtro por mes
            Filter::make('Por mes')
                ->form([
                    Forms\Components\Select::make('mes')
                        ->label('Seleccionar Mes')
                        ->options([
                            '01' => 'Enero',
                            '02' => 'Febrero',
                            '03' => 'Marzo',
                            '04' => 'Abril',
                            '05' => 'Mayo',
                            '06' => 'Junio',
                            '07' => 'Julio',
                            '08' => 'Agosto',
                            '09' => 'Septiembre',
                            '10' => 'Octubre',
                            '11' => 'Noviembre',
                            '12' => 'Diciembre',
                        ]),
                ])
                ->query(function (Builder $query, array $data) {
                    if (isset($data['mes'])) {
                        return $query->whereMonth('fecha_emision', $data['mes']);
                    }
                    return $query;
                }),

        ])
        ->actions([
            // Acción de eliminar
            ActionsAction::make('delete')
                ->label('Eliminar')
                ->icon('heroicon-o-trash') // Puedes cambiar el ícono si lo deseas
                ->color('danger') // Coloca el botón en rojo para que destaque
                ->requiresConfirmation() // Solicitar confirmación antes de eliminar
                ->action(function ($record) {
                    // Eliminar el archivo PDF
                    $pdfPath = storage_path('app/public/uploads/facturas_emitidas/' . $record->pdf_filename); // Ajusta el nombre del archivo según tu almacenamiento
                    if (file_exists($pdfPath)) {
                        unlink($pdfPath); // Eliminar el archivo PDF
                    }

                    // Eliminar el registro de la base de datos
                    $record->delete(); // Eliminar el registro

                    Notification::make()
                        ->title('Éxito')
                        ->body('Factura eliminada con éxito!')
                        ->success()
                        ->send();
            }),

            // Acción de editar
            ActionsAction::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-trash')
                ->modalHeading('Editar Factura')
                    ->form([
                        TextInput::make('folio')->required(),
                        TextInput::make('rfc_emisor')->required(),
                        TextInput::make('nombre_emisor')->required(),
                        TextInput::make('rfc_receptor')->required(),
                        TextInput::make('nombre_receptor')->required(),
                        DateTimePicker::make('fecha_emision')->required(),
                        TextInput::make('subtotal')->numeric()->required(),
                        TextInput::make('iva_trasladado')->numeric()->required(),
                        TextInput::make('iva_retenido')->numeric()->required(),
                        TextInput::make('isr_retenido')->numeric()->required(),
                        TextInput::make('total')->numeric()->required(),
                        TextInput::make('pdf_filename')->required(),
                    ])
                    ->action(function (FacturaEmitida $record, array $data): void {
                        $record->update($data);
                    })
                    ->fillForm(function (FacturaEmitida $record, array $data): array {
                        return $record->toArray();
                    }),

            // Acción para abrir el PDF en un modal
            ActionsAction::make('view_pdf')
                ->label('Ver Factura')  // Texto del botón
                ->icon('heroicon-o-eye')  // Icono del ojo
                ->color('success')  // Botón verde
                ->modalHeading('Factura PDF')  // Título del modal
                ->modalDescription(function ($record){
                    return $record->pdf_filename;
                })
                ->modalContent(function ($record) {
                    // Generar la URL del archivo PDF
                    $pdfUrl = asset('storage/uploads/facturas_emitidas/' . $record->pdf_filename);

                    // Verificar si el archivo realmente existe
                    if (!file_exists(storage_path('app/public/uploads/facturas_emitidas/' . $record->pdf_filename))) {
                        return view('filament.resources.factura-recibida.pdf-viewer', ['error' => 'El archivo PDF no está disponible o no se encuentra.']);
                    }

                    // Pasar la URL a la vista del modal
                    return view('filament.resources.factura-recibida.pdf-viewer', compact('pdfUrl'));
            })
            ->modalWidth(MaxWidth::FiveExtraLarge)  // Tamaño del modal (puedes usar sm, md, lg, xl, 2xl, 3xl, 4xl, etc.)
            ->modalSubmitAction(false)
            ->modalCancelAction(fn (StaticAction $action) => $action->label('Cerrar')),

        ])
        ->recordUrl(null) // Deshabilitar la acción de clic en las filas
        ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturaEmitidas::route('/'),
            'create' => Pages\CreateFacturaEmitida::route('/create'),
            'edit' => Pages\EditFacturaEmitida::route('/{record}/edit'),
        ];
    }
}

