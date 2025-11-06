<?php

namespace App\Services;

use App\Repositories\IMarcaRepository;
use Illuminate\Http\Request;

class MarcaService
{
    protected $marcaRepository;

    public function __construct(IMarcaRepository $marcaRepository)
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

    public function criarMarca(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->marcaRepository->getModel()->rules(), $this->marcaRepository->getModel()->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagem/marcas', 'public');

        // Cria a marca
        return $this->marcaRepository->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
    }

    public function buscarMarca(Request $request, int $id)
    {
        // Lê os atributos solicitados
        $atributos = $request->atributos ?? '*';
        $atributos_modelos = $request->atributos_modelos ?? null;

        // Busca no repositório
        $marca = $this->marcaRepository->findWithModelos($id, $atributos, $atributos_modelos);

        if (!$marca) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        return $marca;
    }
}
