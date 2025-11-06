<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Services\MarcaService;
use Illuminate\Http\Request;
use App\Models\Marca;
use Illuminate\Support\Facades\Log;

class MarcaController extends Controller
{
    protected $marcaService;
    protected $marca;

    public function __construct(MarcaService $marcaService, Marca $marca)
    {
        $this->marcaService = $marcaService;
        $this->marca = $marca;
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
        $marca = $this->marca->find($id);

        if ($marca == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }

        // Validação dinâmica para PATCH ou completa para PUT
        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            foreach ($marca->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        // Preparar dados para atualização
        $dadosAtualizacao = [];

        // Processar imagem apenas se foi enviada
        if ($request->hasFile('imagem')) {
            // Deletar imagem antiga se existir
            if ($marca->imagem) {
                Storage::disk('public')->delete($marca->imagem);
            }

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');
            $dadosAtualizacao['imagem'] = $imagem_urn;
        }

        // Adicionar nome apenas se foi enviado (para PATCH)
        if ($request->has('nome')) {
            $dadosAtualizacao['nome'] = $request->nome;
        }

        // Atualizar apenas se houver dados
        if (!empty($dadosAtualizacao)) {
            $marca->update($dadosAtualizacao);
        }

        // Recarregar o modelo para obter dados atualizados
        $marca->refresh();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $marca = $this->marca->find($id);
        if ($marca == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }


        Storage::disk('public')->delete($marca->imagem);


        $marca->delete();

        return response()->json(['msg' => 'Marca removida com sucesso!']);
    }
}
