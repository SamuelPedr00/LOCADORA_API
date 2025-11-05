<?php

namespace App\Services;

use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaService
{
    protected $marcaRepository;

    public function __construct(MarcaRepository $marcaRepository)
    {
        $this->marcaRepository = $marcaRepository;
    }

    public function listarMarcas(Request $request)
    {

        $columns = ['*'];
        $relations = [];

        // Aplica filtros (essas regras pertencem à camada de negócio)
        if ($request->has('atributos')) {
            $columns = explode(',', $request->atributos);
        }

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = $request->atributos_modelos;
            $relations = ['modelos:id,' . $atributos_modelos];
        } else {
            $relations = ['modelos'];
        }


        return $this->marcaRepository->getAll($columns, $relations);
    }
}
