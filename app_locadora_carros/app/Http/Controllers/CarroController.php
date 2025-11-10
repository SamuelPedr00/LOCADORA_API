<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Services\CarroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CarroController extends Controller
{

    protected $carroService;

    public function __construct(CarroService $carroService)
    {
        $this->carroService = $carroService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $carros = $this->carroService->listarCarros($request);

            if ($carros->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma carro encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json($carros, 200);
        } catch (\Exception $e) {
            // Loga o erro (opcional, mas muito recomendado)
            Log::error('Erro ao listar carros: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro genérico pro cliente
            return response()->json([
                'error' => 'Ocorreu um erro ao buscar as carros.',
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
            $carro = $this->carroService->criarCarro($request);
            return response()->json($carro, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar carro: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar carro'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $carro = $this->carroService->buscarCarro($request, $id);
            return response()->json($carro, 200);
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
            $carro = $this->carroService->atualizarCarro($request, $id);
            return response()->json($carro, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar carro: ' . $e->getMessage());

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
            $mensagem = $this->carroService->removerCarro($id);
            return response()->json($mensagem, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao remover carro: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
