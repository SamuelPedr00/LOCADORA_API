<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    private $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = $this->marca->all();
        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagem/marcas', 'public');

        // Criar a marca após a validação
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $marca = $this->marca->find($id);
        if ($marca == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }
        return response()->json($marca, 200);
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
