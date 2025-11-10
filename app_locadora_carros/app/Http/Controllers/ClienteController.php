<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ClienteService;

class ClienteController extends Controller
{

    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $clientes = $this->clienteService->listarClientes($request);

            if ($clientes->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma carro encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json($clientes, 200);
        } catch (\Exception $e) {
            // Loga o erro (opcional, mas muito recomendado)
            Log::error('Erro ao listar clientes: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna erro genérico pro cliente
            return response()->json([
                'error' => 'Ocorreu um erro ao buscar as clientes.',
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
            $cliente = $this->clienteService->criarCliente($request);
            return response()->json($cliente, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar cliente'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $cliente = $this->clienteService->buscarCliente($request, $id);
            return response()->json($cliente, 200);
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
            $cliente = $this->clienteService->atualizarCliente($request, $id);
            return response()->json($cliente, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cliente: ' . $e->getMessage());

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
            $mensagem = $this->clienteService->removerCliente($id);
            return response()->json($mensagem, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao remover cliente: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
