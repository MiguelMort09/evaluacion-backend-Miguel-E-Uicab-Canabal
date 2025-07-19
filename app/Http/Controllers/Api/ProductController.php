<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResourse;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener parámetros de filtrado y paginación
        $filters = $request->only([
            'search',
            'category',
            'status',
            'min_price',
            'max_price',
            'has_stock',
            'user_id',
            'sort_by',
            'sort_direction'
        ]);

        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Máximo 100 elementos por página

        // Aplicar filtros y paginación
        $products = Product::with('user')
            ->filter($filters)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
            'filters_applied' => array_filter($filters), // Solo mostrar filtros con valores
            'message' => 'Productos obtenidos exitosamente',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        // Asignar el user_id del usuario autenticado
        $validatedData['user_id'] = $request->user()->id;

        $product = Product::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => ProductResourse::collection($product),
            'message' => 'Producto obtenido exitosamente'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }
}
