<?php

namespace App\Http\Controllers;

use App\Services\MarcaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarcaController extends Controller
{
    protected $marcaService;

    public function __construct(MarcaService $marcaService)
    {
        $this->marcaService = $marcaService;
    }

    public function index(Request $request)
    {
        try {
            $marcas = $this->marcaService->listarMarcas($request);

            if ($marcas->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma marca encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json($marcas, 200);
        } catch (\Exception $e) {
            // Loga o erro (opcional, mas muito recomendado)
            Log::error('Erro ao listar marcas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro genérico pro cliente
            return response()->json([
                'error' => 'Ocorreu um erro ao buscar as marcas.',
                'details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $marca = $this->marcaService->criarMarca($request);
            return response()->json($marca, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar marca: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar marca'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $marca = $this->marcaService->buscarMarca($request, $id);
            return response()->json($marca, 200);
        } catch (\Exception $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            $marca = $this->marcaService->atualizarMarca($request, $id);
            return response()->json($marca, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar marca: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $mensagem = $this->marcaService->removerMarca($id);
            return response()->json($mensagem, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao remover marca: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
