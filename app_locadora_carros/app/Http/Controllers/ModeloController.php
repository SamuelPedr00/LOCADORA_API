<?php

namespace App\Http\Controllers;

use App\Services\ModeloService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ModeloController extends Controller
{

    protected $modeloService;

    public function __construct(ModeloService $modeloService)
    {
        $this->modeloService = $modeloService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {
            $modelos = $this->modeloService->listarModelos($request);

            if ($modelos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma carro encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json($modelos, 200);
        } catch (\Exception $e) {
            // Loga o erro (opcional, mas muito recomendado)
            Log::error('Erro ao listar modelos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro genérico pro cliente
            return response()->json([
                'error' => 'Ocorreu um erro ao buscar as modelos.',
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
            $modelo = $this->modeloService->criarModelo($request);
            return response()->json($modelo, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar modelo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar modelo'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $modelo = $this->modeloService->buscarModelo($request, $id);
            return response()->json($modelo, 200);
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
            $modelo = $this->modeloService->atualizarModelo($request, $id);
            return response()->json($modelo, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar modelo: ' . $e->getMessage());

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
            $mensagem = $this->modeloService->removerModelo($id);
            return response()->json($mensagem, 200);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                // Erro de integridade referencial (FK)
                return response()->json([
                    'error' => 'Não é possível excluir este modelo pois existem carros associados a ele.'
                ], 409); // 409 Conflict
            }

            Log::error('Erro de banco ao remover modelo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro no banco de dados.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao remover modelo: ' . $e->getMessage());
            $code = (int) $e->getCode();
            if ($code < 100 || $code > 599) $code = 500;
            return response()->json(['error' => $e->getMessage()], $code);
        }
    }
}
