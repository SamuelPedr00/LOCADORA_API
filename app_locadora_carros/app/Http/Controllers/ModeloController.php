<?php

namespace App\Http\Controllers;

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
    public function show(Modelo $modelo)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modelo $modelo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modelo $modelo)
    {
        //
    }
}
