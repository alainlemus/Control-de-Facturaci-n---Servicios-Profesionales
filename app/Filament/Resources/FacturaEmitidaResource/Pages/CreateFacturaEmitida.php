<?php

namespace App\Filament\Resources\FacturaEmitidaResource\Pages;

use App\Filament\Resources\FacturaEmitidaResource;
use App\Models\FacturaEmitida;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateFacturaEmitida extends CreateRecord
{
    protected static string $resource = FacturaEmitidaResource::class;

    protected function handleRecordCreation(array $data): FacturaEmitida
    {
        DB::beginTransaction();

        try {
            $ultimoRegistro = null;

            if (isset($data['archivos']) && is_array($data['archivos'])) {
                foreach ($data['archivos'] as $archivo) {
                    $rutaArchivo = storage_path('app/' . $archivo);

                    if (!file_exists($rutaArchivo)) {
                        Log::error("Archivo no encontrado: " . $rutaArchivo);
                        continue; // Pasa al siguiente archivo
                    }

                    // Procesar el archivo PDF para extraer datos
                    $datosExtraidos = FacturaEmitida::procesarPDF($rutaArchivo);
                    // Agregar cliente_id al array de datos extraídos
                    $datosExtraidos['cliente_id'] = $data['cliente_id'];

                    // Crear registro en la base de datos
                    $ultimoRegistro = FacturaEmitida::create($datosExtraidos);
                }
            }

            DB::commit();

            Notification::make()
                ->title('Éxito')
                ->body('Archivos subidos y datos guardados correctamente.')
                ->success()
                ->send();

            return $ultimoRegistro ?? new FacturaEmitida();
        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivos subidos
            if (isset($data['archivos']) && is_array($data['archivos'])) {
                foreach ($data['archivos'] as $archivo) {
                    $rutaArchivo = storage_path('app/' . $archivo);
                    if (file_exists($rutaArchivo)) {
                        unlink($rutaArchivo);
                    }
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
            foreach ($data['archivos'] as $archivo) {
                $rutaArchivo = storage_path('app/' . $archivo);

                if (file_exists($rutaArchivo)) {
                    // Procesar el archivo PDF para extraer datos
                    $datosExtraidos = FacturaEmitida::procesarPDF($rutaArchivo);
                    // Agregar cliente_id al array de datos extraídos
                    $datosExtraidos['cliente_id'] = $data['cliente_id'];

                    // Crear registro en la base de datos
                    $ultimoRegistro = FacturaEmitida::create($datosExtraidos);
                }
            }
        }

        // Retornar el último registro creado
        return $ultimoRegistro ?? new FacturaEmitida();*/
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index'); // Redirige al índice después de crear
    }
}
