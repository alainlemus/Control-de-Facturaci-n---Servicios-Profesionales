<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaRecibidaResource\Pages;
use App\Models\FacturaRecibida;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class FacturaRecibidaResource extends Resource
{
    protected static ?string $model = FacturaRecibida::class;

    //protected static ?string $label = 'Cargar Facturas PDF';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('archivos')
                    ->label('Cargar PDF(s)')
                    ->multiple()
                    ->disk('local') // Cambia 'local' si usas otro disco configurado.
                    ->directory('public/uploads/facturas') // Directorio donde se guardarán los PDFs.
                    ->acceptedFileTypes(['application/pdf']) // Solo permite PDFs.
                    ->maxFiles(100) // Número máximo de archivos.
                    ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rfc_emisor')
                    ->label('RFC emisor'),
                TextColumn::make('nombre_emisor')
                    ->label('Emisor')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('descuento')
                    ->label('Descuento')
                    ->sortable()
                    ->searchable()
                    ->money('mxn'), // Formatea como moneda MXN
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->sortable()
                    ->searchable()
                    ->money('mxn'), // Formatea como moneda MXN
                TextColumn::make('iva')
                    ->label('IVA')
                    ->sortable()
                    ->searchable()
                    ->money('mxn'),
                TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->searchable()
                    ->money('mxn'),
                TextColumn::make('fecha_emision')
                    ->label('Fecha de emisión')
                    ->sortable()
                    ->dateTime('d/m/Y H:i'), // Formato personalizado de fecha y hora
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->sortable()
                    ->dateTime('d/m/Y H:i') // Formato personalizado de fecha y hora
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
                            if ($record->pdf_filename && file_exists(storage_path('app/public/uploads/facturas/' . $record->pdf_filename))) {
                                unlink(storage_path('app/public/uploads/facturas/' . $record->pdf_filename));
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
            ->header(function (Table $table) {

                // Inicializar los totales a 0
                $totales = (object) ['subtotal' => 0, 'iva' => 0, 'total' => 0];

                // Inicializamos la consulta base para obtener los totales
                $query = FacturaRecibida::query();

                // Obtener los filtros aplicados a la tabla usando el método getFilters
                $filters = $table->getFilters();  // Filament maneja los filtros internamente

                // Aplicar el filtro de mes si está presente
                foreach ($filters as $filter) {
                    // Verificar si el filtro es el de 'Por mes'
                    if ($filter->getName() === 'Por mes') {
                        // Obtener el valor del mes seleccionado (por ejemplo, '03' para marzo)
                        $mes = $filter->getState();  // El estado del filtro 'mes'

                        // Aplicar el filtro por mes a la consulta
                        $query->whereMonth('fecha_emision', $mes);
                    }
                }

                // Ahora calcular los totales con la consulta filtrada
                $totales = $query->selectRaw('SUM(subtotal) as subtotal, SUM(total) as total')->first();

                // Si no hay registros, asignamos 0 a los totales
                $totales = $totales ? $totales : (object) ['subtotal' => 0, 'total' => 0];

                $iva = 0.16 * $totales->subtotal;

                // Pasar los totales a la vista
                return view('filament.resources.factura-recibida.totales', [
                    'subtotal' => $totales->subtotal ?? 0,
                    'iva' => $iva ?? 0,
                    'total' => $totales->total ?? 0,
                ]);
            })
            ->actions([
                // Acción de eliminar
                ActionsAction::make('delete')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash') // Puedes cambiar el ícono si lo deseas
                    ->color('danger') // Coloca el botón en rojo para que destaque
                    ->requiresConfirmation() // Solicitar confirmación antes de eliminar
                    ->action(function ($record) {
                        // Eliminar el archivo PDF
                        $pdfPath = storage_path('app/public/uploads/facturas/' . $record->pdf_filename); // Ajusta el nombre del archivo según tu almacenamiento
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
                        TextInput::make('pdf_filename')->required(),
                        TextInput::make('folio')->required(),
                        TextInput::make('rfc_emisor')->required(),
                        TextInput::make('nombre_emisor')->required(),
                        TextInput::make('rfc_receptor')->required(),
                        TextInput::make('nombre_receptor')->required(),
                        DateTimePicker::make('fecha_emision')->required(),
                        TextInput::make('uso_cfdi')->required(),
                        TextInput::make('subtotal')->numeric()->required(),
                        TextInput::make('descuento')->numeric()->required(),
                        TextInput::make('iva')->numeric()->required(),
                        TextInput::make('total')->numeric()->required(),
                    ])
                    ->action(function (FacturaRecibida $record, array $data): void {
                        $record->update($data);
                    })
                    ->fillForm(function (FacturaRecibida $record, array $data): array {
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
                        $pdfUrl = asset('storage/uploads/facturas/' . $record->pdf_filename);

                        // Verificar si el archivo realmente existe
                        if (!file_exists(storage_path('app/public/uploads/facturas/' . $record->pdf_filename))) {
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturaRecibidas::route('/'),
            'create' => Pages\CreateFacturaRecibida::route('/create'),
            'edit' => Pages\EditFacturaRecibida::route('/{record}/edit'),
        ];
    }

}
