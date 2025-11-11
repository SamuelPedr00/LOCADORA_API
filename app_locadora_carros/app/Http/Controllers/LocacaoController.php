<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\LocacaoService;

class LocacaoController extends Controller
{

    protected $locacaoService;

    public function __construct(LocacaoService $locacaoService)
    {
        $this->locacaoService = $locacaoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $locacoes = $this->locacaoService->listarLocacoes($request);

            if ($locacoes->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma locacoes encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json($locacoes, 200);
        } catch (\Exception $e) {
            // Loga o erro (opcional, mas muito recomendado)
            Log::error('Erro ao listar locacoes: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro genérico pro cliente
            return response()->json([
                'error' => 'Ocorreu um erro ao buscar as locacoes.',
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
            $locacao = $this->locacaoService->criarlocacao($request);
            return response()->json($locacao, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar locacao: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar locacao'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $locacao = $this->locacaoService->buscarLocacao($request, $id);
            return response()->json($locacao, 200);
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
            $locacao = $this->locacaoService->atualizarLocacao($request, $id);
            return response()->json($locacao, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar locacao: ' . $e->getMessage());

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
            $mensagem = $this->locacaoService->removerLocacao($id);
            return response()->json($mensagem, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao remover Locacao: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
