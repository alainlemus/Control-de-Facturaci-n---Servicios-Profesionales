<?php

namespace App\Filament\Widgets;

use App\Models\FacturaRecibida;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class FacturasRecibidasChart extends ChartWidget
{
    protected static ?string $heading = 'Gastos por mes.';
    public ?string $filter = '2025';

    public function getDescription(): ?string
    {
        return 'Selecciona el año a consultar';
    }

    protected function getFilters(): ?array
    {
        // Rango de años (puedes ajustar según sea necesario)
        $startYear = now()->year - 5; // Hace 5 años
        $endYear = now()->year; // Un año en el futuro opcionalmente

        // Generar el array de filtros dinámicamente
        $years = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $years[$year] = (string) $year;
        }

        return $years;
    }

    protected function getData(): array
    {
        setlocale(LC_TIME, 'es_ES');
        \Carbon\Carbon::setLocale('es');

        $data = Trend::query(FacturaRecibida::query())
            ->between(
                start: Carbon::createFromDate($this->filter)->startOfYear(),
                end: Carbon::createFromDate($this->filter)->endOfYear(),
            )
            ->dateColumn('fecha_emision')
            ->perMonth()
            ->sum('total');

        $dataIva = Trend::query(FacturaRecibida::query())
            ->between(
                start: Carbon::createFromDate($this->filter)->startOfYear(),
                end: Carbon::createFromDate($this->filter)->endOfYear(),
            )
            ->dateColumn('fecha_emision')
            ->perMonth()
            ->sum('iva');

            //dd($data);

        return [
            'datasets' => [
                [
                    'label' => 'Gastos en los meses del año.',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3bc60b',
                    'backgroundColor' => '#3bc60b',
                ],
                [
                    'label' => 'IVA en los meses del año.',
                    'data' => $dataIva->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9BD0F5',
                    'backgroundColor' => '#9BD0F5',
                ]
            ],
            'labels' => $data->map(fn (TrendValue $value) => \Carbon\Carbon::parse($value->date)->format('F')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
