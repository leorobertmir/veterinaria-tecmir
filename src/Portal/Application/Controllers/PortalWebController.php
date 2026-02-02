<?php

namespace Src\Portal\Application\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Src\Cita\Infrastructure\Mappers\CitaMapper;
use Src\Cita\Infrastructure\Models\CitaEloquentModel;
use Src\Cliente\Infrastructure\Models\ClienteEloquentModel;
use Src\Mascota\Infrastructure\Mappers\MascotaMapper;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;

class PortalWebController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        if (!$user) {
            return Inertia::render('Portal/index', [
                'mascotas' => [],
                'citas' => [],
            ]);
        }

        $cliente = ClienteEloquentModel::where('email', $user->email)->first();

        if (!$cliente) {
            return Inertia::render('Portal/index', [
                'mascotas' => [],
                'citas' => [],
            ]);
        }

        $mascotas = MascotaEloquentModel::where('cliente_id', $cliente->id)
            ->with('citas')
            ->get();

        $mascotasData = $mascotas->map(
            fn($model) => MascotaMapper::toDomain($model)->toArray()
        )->toArray();

        $citas = CitaEloquentModel::whereIn('mascota_id', $mascotas->pluck('id'))
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $citasData = $citas->map(
            fn($model) => CitaMapper::toDomain($model)->toArray()
        )->toArray();

        return Inertia::render('Portal/index', [
            'cliente' => [
                'id' => $cliente->id,
                'razonSocial' => $cliente->razon_social,
                'email' => $cliente->email,
            ],
            'mascotas' => $mascotasData,
            'citas' => $citasData,
        ]);
    }
}
