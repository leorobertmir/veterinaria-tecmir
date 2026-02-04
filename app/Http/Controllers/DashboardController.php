<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Carbon;
use Src\Factura\Infrastructure\Models\FacturaEloquentModel;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal
     */
    public function index(): Response
    {
        $revenue = (float) FacturaEloquentModel::sum('total');
        $orders = FacturaEloquentModel::count();
        $customers = ClienteEloquentModel::count();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $previousMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $currentMonthRevenue = (float) FacturaEloquentModel::whereBetween('fecha_emision', [$currentMonthStart, Carbon::now()])
            ->sum('total');
        $previousMonthRevenue = (float) FacturaEloquentModel::whereBetween('fecha_emision', [$previousMonthStart, $previousMonthEnd])
            ->sum('total');

        $growth = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2)
            : 0.0;

        $salesStart = Carbon::now()->subDays(6)->startOfDay();
        $salesEnd = Carbon::now()->endOfDay();

        $sales = FacturaEloquentModel::selectRaw('DATE(fecha_emision) as date, SUM(total) as amount')
            ->whereBetween('fecha_emision', [$salesStart, $salesEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $salesData = [];
        foreach (Carbon::now()->subDays(6)->daysUntil(Carbon::now()) as $date) {
            $key = $date->toDateString();
            $amount = isset($sales[$key]) ? (float) $sales[$key]->amount : 0.0;
            $salesData[] = [
                'date' => $key,
                'amount' => $amount,
            ];
        }

        return Inertia::render('Dashboard', [
            'revenue' => $revenue,
            'orders' => $orders,
            'customers' => $customers,
            'growth' => $growth,
            'salesData' => $salesData,
        ]);
    }
}
