<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    private $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->modelo->all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->modelo->rules(), $this->modelo->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagem/modelos', 'public');

        // Criar o modelo após a validação
        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }
        return response()->json($modelo, 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }

        // Validação dinâmica para PATCH ou completa para PUT
        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            foreach ($modelo->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $modelo->feedback());
        } else {
            $request->validate($modelo->rules(), $modelo->feedback());
        }

        // Preparar dados para atualização
        $dadosAtualizacao = $request->only([
            'marca_id',
            'nome',
            'numero_portas',
            'lugares',
            'air_bag',
            'abs'
        ]);

        // Processar imagem apenas se foi enviada
        if ($request->hasFile('imagem')) {
            // Deletar imagem antiga
            if ($modelo->imagem) {
                Storage::disk('public')->delete($modelo->imagem);
            }

            // Armazenar nova imagem
            $imagem = $request->file('imagem');
            $dadosAtualizacao['imagem'] = $imagem->store('imagem', 'public');
        }

        // Atualizar apenas os campos enviados (importante para PATCH)
        $modelo->update($dadosAtualizacao);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo == null) {
            return response()->json(['error' => 'Pesquisa não encontrada'], 404);
        }
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete();

        return response()->json(['msg' => 'Marca removida com sucesso!']);
    }
}
