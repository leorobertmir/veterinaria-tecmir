<?php

namespace Src\Reportes\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReporteWebController extends Controller
{
    public function index(): JsonResponse
    {
        $ventasTotales = DB::table('facturas')
            ->where('estado', '!=', 'anulada')
            ->sum('total');

        $ventasPorMes = DB::table('facturas')
            ->selectRaw("TO_CHAR(fecha_emision, 'YYYY-MM') as periodo, SUM(total) as total")
            ->where('estado', '!=', 'anulada')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get();

        $topProductos = DB::table('detalle_facturas')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->where('facturas.estado', '!=', 'anulada')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(detalle_facturas.cantidad) as cantidad'),
                DB::raw('SUM(detalle_facturas.subtotal) as total')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get();

        return response()->json([
            'ventas_totales' => (float) $ventasTotales,
            'ventas_por_mes' => $ventasPorMes,
            'top_productos' => $topProductos,
        ]);
    }
}
