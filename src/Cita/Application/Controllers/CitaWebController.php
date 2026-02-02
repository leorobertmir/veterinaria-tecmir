<?php

namespace Src\Cita\Application\Controllers;

use App\Http\Controllers\Controller;
use Src\Cita\Infrastructure\Models\CitaEloquentModel;
use Src\Cita\Infrastructure\Mappers\CitaMapper;
use Src\Cita\Infrastructure\Requests\StoreCitaRequest;
use Src\Cita\Infrastructure\Requests\UpdateCitaRequest;
use Src\Mascota\Infrastructure\Models\MascotaEloquentModel;
use Src\Auth\Infrastructure\Models\UserEloquentModel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class CitaWebController extends Controller
{
    public function index(Request $request): Response
    {
        $query = CitaEloquentModel::with(['mascota.cliente', 'veterinario']);

        // Filtrar por fecha si se proporciona
        if ($request->has('fecha')) {
            $fecha = Carbon::parse($request->fecha);
            $query->whereDate('fecha_hora', $fecha);
        }

        // Filtrar por estado
        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // Filtrar por veterinario
        if ($request->has('veterinario_id') && $request->veterinario_id) {
            $query->where('veterinario_id', $request->veterinario_id);
        }

        $citas = $query->orderBy('fecha_hora', 'asc')->get();

        $citasData = $citas->map(function ($model) {
            $data = CitaMapper::toDomain($model)->toArray();
            $data['mascota'] = $model->mascota ? [
                'id' => $model->mascota->id,
                'nombre' => $model->mascota->nombre,
                'especie' => $model->mascota->especie,
                'raza' => $model->mascota->raza,
                'cliente' => $model->mascota->cliente ? [
                    'id' => $model->mascota->cliente->id,
                    'razonSocial' => $model->mascota->cliente->razon_social,
                    'telefono' => $model->mascota->cliente->telefono,
                ] : null,
            ] : null;
            $data['veterinario'] = $model->veterinario ? [
                'id' => $model->veterinario->id,
                'name' => $model->veterinario->name,
            ] : null;
            return $data;
        })->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        // Estadísticas del día
        $hoy = Carbon::today();
        $citasHoy = CitaEloquentModel::whereDate('fecha_hora', $hoy);

        return Inertia::render('Cita/index', [
            'citas' => [
                'data' => $citasData,
                'meta' => [
                    'total' => count($citasData),
                ]
            ],
            'veterinarios' => $veterinarios,
            'stats' => [
                'hoy' => $citasHoy->count(),
                'programadas' => $citasHoy->clone()->where('estado', 'programada')->count(),
                'completadas' => $citasHoy->clone()->where('estado', 'completada')->count(),
                'canceladas' => $citasHoy->clone()->where('estado', 'cancelada')->count(),
            ],
            'filters' => [
                'fecha' => $request->fecha ?? Carbon::today()->format('Y-m-d'),
                'estado' => $request->estado ?? 'todos',
                'veterinario_id' => $request->veterinario_id ?? null,
            ],
        ]);
    }

    public function calendar(): Response
    {
        $citas = CitaEloquentModel::with(['mascota.cliente', 'veterinario'])
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
            ->get();

        $citasData = $citas->map(function ($model) {
            return [
                'id' => $model->id,
                'title' => $model->mascota->nombre . ' - ' . $model->tipo_consulta,
                'start' => $model->fecha_hora->format('Y-m-d\TH:i:s'),
                'end' => $model->fecha_hora_fin?->format('Y-m-d\TH:i:s') ?? $model->fecha_hora->addHour()->format('Y-m-d\TH:i:s'),
                'estado' => $model->estado,
                'tipoConsulta' => $model->tipo_consulta,
                'mascota' => [
                    'id' => $model->mascota->id,
                    'nombre' => $model->mascota->nombre,
                ],
                'veterinario' => $model->veterinario ? [
                    'id' => $model->veterinario->id,
                    'name' => $model->veterinario->name,
                ] : null,
            ];
        })->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        return Inertia::render('Cita/calendar', [
            'citas' => $citasData,
            'veterinarios' => $veterinarios,
        ]);
    }

    public function create(Request $request): Response
    {
        $mascotas = MascotaEloquentModel::with('cliente')
            ->where('activo', true)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'especie' => $m->especie,
                'cliente' => $m->cliente ? [
                    'id' => $m->cliente->id,
                    'razonSocial' => $m->cliente->razon_social,
                ] : null,
            ])->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        return Inertia::render('Cita/create', [
            'mascotas' => $mascotas,
            'veterinarios' => $veterinarios,
            'selectedMascotaId' => $request->mascota_id,
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? 'programada';

            CitaEloquentModel::create($data);

            return redirect()
                ->route('citas.index')
                ->with('success', 'Cita programada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al programar la cita: ' . $e->getMessage());
        }
    }

    public function show(string $id): Response
    {
        $cita = CitaEloquentModel::with(['mascota.cliente', 'veterinario', 'historiaClinica'])->findOrFail($id);

        $citaData = CitaMapper::toDomain($cita)->toArray();
        $citaData['mascota'] = $cita->mascota ? [
            'id' => $cita->mascota->id,
            'nombre' => $cita->mascota->nombre,
            'especie' => $cita->mascota->especie,
            'raza' => $cita->mascota->raza,
            'cliente' => $cita->mascota->cliente ? [
                'id' => $cita->mascota->cliente->id,
                'razonSocial' => $cita->mascota->cliente->razon_social,
                'telefono' => $cita->mascota->cliente->telefono,
                'email' => $cita->mascota->cliente->email,
            ] : null,
        ] : null;
        $citaData['veterinario'] = $cita->veterinario ? [
            'id' => $cita->veterinario->id,
            'name' => $cita->veterinario->name,
        ] : null;
        $citaData['tieneHistoriaClinica'] = $cita->historiaClinica !== null;

        return Inertia::render('Cita/show', [
            'cita' => $citaData,
        ]);
    }

    public function edit(string $id): Response
    {
        $cita = CitaEloquentModel::findOrFail($id);

        $mascotas = MascotaEloquentModel::with('cliente')
            ->where('activo', true)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'especie' => $m->especie,
                'cliente' => $m->cliente ? [
                    'id' => $m->cliente->id,
                    'razonSocial' => $m->cliente->razon_social,
                ] : null,
            ])->toArray();

        $veterinarios = UserEloquentModel::all()->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
        ])->toArray();

        return Inertia::render('Cita/edit', [
            'cita' => CitaMapper::toDomain($cita)->toArray(),
            'mascotas' => $mascotas,
            'veterinarios' => $veterinarios,
        ]);
    }

    public function update(UpdateCitaRequest $request, string $id): RedirectResponse
    {
        try {
            $cita = CitaEloquentModel::findOrFail($id);
            $cita->update($request->validated());

            return redirect()
                ->route('citas.index')
                ->with('success', 'Cita actualizada exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la cita: ' . $e->getMessage());
        }
    }

    public function updateEstado(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:programada,confirmada,en_curso,completada,cancelada,no_asistio'
        ]);

        try {
            $cita = CitaEloquentModel::findOrFail($id);
            $cita->update(['estado' => $request->estado]);

            return redirect()
                ->back()
                ->with('success', 'Estado de cita actualizado');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $cita = CitaEloquentModel::find($id);

        if (!$cita) {
            return redirect()
                ->back()
                ->with('error', 'Cita no encontrada');
        }

        if ($cita->historiaClinica) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar esta cita porque tiene historia clínica asociada');
        }

        $cita->delete();

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita eliminada exitosamente');
    }
}
