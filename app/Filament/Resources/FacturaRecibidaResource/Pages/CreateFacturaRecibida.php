<?php

namespace App\Filament\Resources\FacturaRecibidaResource\Pages;

use App\Filament\Resources\FacturaRecibidaResource;
use App\Models\FacturaRecibida;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CreateFacturaRecibida extends CreateRecord
{
    protected static string $resource = FacturaRecibidaResource::class;

    protected static ?string $title = 'Cargar Facturas PDF';

    protected function getButtonLabel(): string
    {
        return 'Cargar Facturas PDF';  // Aquí cambiamos el texto del botón de creación
    }

    protected function handleRecordCreation(array $data): FacturaRecibida
    {
        DB::beginTransaction();

        try{
            foreach ($data['archivos'] as $archivo) {
                $rutaArchivo = storage_path('app/' . $archivo);

                if (!file_exists($rutaArchivo)) {
                    Log::error("Archivo no encontrado: " . $rutaArchivo);
                    continue; // Pasa al siguiente archivo
                }

                // Procesar el archivo para extraer datos
                $datosExtraidos = FacturaRecibida::procesarPDF($rutaArchivo);

                // Crear registro en la base de datos
                $ultimoRegistro = FacturaRecibida::create($datosExtraidos);
            }

            DB::commit();

            Notification::make()
                ->title('Éxito')
                ->body('Archivos subidos y datos guardados correctamente.')
                ->success()
                ->send();

            return $ultimoRegistro;

        }catch(\Exception $e){
            DB::rollBack();

            // Eliminar archivos subidos
            foreach ($data['archivos'] as $archivo) {
                $rutaArchivo = storage_path('app/' . $archivo);
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }

            Log::error("Error al procesar los archivos: " . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->body('Hubo un error al subir los archivos o guardar los datos. Por favor, inténtalo de nuevo.')
                ->danger()
                ->send();

            throw $e;
        }

        /*if (isset($data['archivos']) && is_array($data['archivos'])) {
            foreach ($data['archivos'] as $rutaRelativa) {
                $rutaArchivo = storage_path('app/' . $rutaRelativa);

                // Verifica si el archivo existe
                if (!file_exists($rutaArchivo)) {
                    Log::error("Archivo no encontrado: " . $rutaArchivo);
                    continue; // Pasa al siguiente archivo
                }

                // Procesar el archivo para extraer datos
                $datosExtraidos = FacturaRecibida::procesarPDF($rutaArchivo);

                // Crear registro en la base de datos
                $ultimoRegistro = FacturaRecibida::create($datosExtraidos);
            }
        }

        // Retornar el último registro creado
        return $ultimoRegistro;*/
    }

}
