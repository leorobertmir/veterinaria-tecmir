<?php

namespace Src\Producto\Application\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Src\Categoria\Infrastructure\Models\CategoriaEloquentModel;
use Src\Producto\Infrastructure\Mappers\ProductoMapper;
use Src\Producto\Infrastructure\Models\ProductoEloquentModel;
use Src\Producto\Infrastructure\Requests\StoreProductoRequest;
use Src\Producto\Infrastructure\Requests\UpdateProductoRequest;

class ProductoWebController extends Controller
{
    public function index(): Response
    {
        $productos = ProductoEloquentModel::all();

        $productosData = $productos->map(
            fn($model) => ProductoMapper::toDomain($model)->toArray()
        )->toArray();

        $total = count($productosData);
        $active = $productos->where('activo', true)->count();
        $lowStock = $productos->where('stock', '<', 5)->count();

        return Inertia::render('Productos/index', [
            'productos' => [
                'data' => $productosData,
                'links' => [],
                'meta' => [
                    'total' => $total,
                    'per_page' => $total,
                    'current_page' => 1,
                ]
            ],
            'stats' => [
                'total' => $total,
                'active' => $active,
                'lowStock' => $lowStock,
            ],
        ]);
    }

    public function create(): Response
    {
        $categorias = CategoriaEloquentModel::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Productos/create', [
            'categorias' => $categorias->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
            ])->toArray(),
        ]);
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        try {
            ProductoEloquentModel::create($request->validated());

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto creado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        $producto = ProductoEloquentModel::findOrFail($id);
        $categorias = CategoriaEloquentModel::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Productos/edit', [
            'producto' => ProductoMapper::toDomain($producto)->toArray(),
            'categorias' => $categorias->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
            ])->toArray(),
        ]);
    }

    public function update(UpdateProductoRequest $request, string $id): RedirectResponse
    {
        try {
            $producto = ProductoEloquentModel::findOrFail($id);
            $producto->update($request->validated());

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $producto = ProductoEloquentModel::find($id);

        if (!$producto) {
            return redirect()
                ->back()
                ->with('error', 'Producto no encontrado');
        }

        if ($producto->detalleFacturas()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar este producto porque tiene facturas asociadas');
        }

        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
