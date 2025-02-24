<?php

namespace App\Filament\Widgets;

use App\Models\FacturaEmitida;
use App\Models\FacturaRecibida;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class FacturasEmitidasTotales extends BaseWidget
{
    protected ?string $heading = 'Estadisticas Generales';
    protected ?string $description = 'En las tarjetas se muestra información de ingresos, gastos y iva total.';

    protected function getStats(): array
    {

        $totalDineroFacturasEmitidas = FacturaEmitida::sum('total');
        $subtotal = FacturaRecibida::sum('subtotal');

        $iva = $subtotal * 0.16;
        $total = $iva + $subtotal;

        return [
            Stat::make('INGRESOS TOTALES', '$' . number_format($totalDineroFacturasEmitidas, 2), 'success')
                ->icon('heroicon-m-currency-dollar', IconPosition::Before),
            Stat::make('GASTOS TOTALES', '$' . number_format($total, 2))
                ->icon('heroicon-o-banknotes', IconPosition::Before),
            Stat::make('IVA DE GASTOS TOTALES', '$' . number_format($iva, 2))
                ->icon('heroicon-m-arrow-trending-up', IconPosition::Before),
        ];
    }

}
