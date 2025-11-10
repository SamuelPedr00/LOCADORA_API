<?php

namespace App\Services;

use App\Repositories\ICarroRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CarroService
{
    protected $carroRepository;

    public function __construct(ICarroRepository $carroRepository)
    {
        $this->carroRepository = $carroRepository;
    }

    public function listarCarros(Request $request)
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


        return $this->carroRepository->getAll($columns, $relations);
    }

    public function criarCarro(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->carroRepository->getModel()->rules(), $this->carroRepository->getModel()->feedback());

        // Cria a carro
        return $this->carroRepository->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);
    }

    public function buscarCarro(Request $request, int $id)
    {
        // Lê os atributos solicitados
        $atributos = $request->atributos ?? '*';
        $atributos_modelos = $request->atributos_modelos ?? null;

        // Busca no repositório
        $carro = $this->carroRepository->findWithModelos($id, $atributos, $atributos_modelos);

        if (!$carro) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        return $carro;
    }

    public function atualizarCarro(Request $request, int $id)
    {
        $carro = $this->carroRepository->find($id);

        if (!$carro) {
            return response()->json(['error' => 'Carro não encontrado'], 404);
        }

        // Define as regras (dinâmicas no PATCH)
        $regras = $request->method() === 'PATCH'
            ? collect($carro->rules())->only(array_keys($request->all()))->toArray()
            : $carro->rules();

        // Laravel lança 422 automaticamente se houver erro
        $validated = $request->validate($regras, $carro->feedback());

        // Atualiza apenas os campos enviados
        $dados = $request->only(['modelo_id', 'km', 'disponivel', 'placa']);

        if (empty($dados)) {
            return response()->json(['error' => 'Nenhum dado foi enviado para atualização'], 400);
        }

        $carroAtualizado = $this->carroRepository->update($id, $dados);

        if (!$carroAtualizado) {
            return response()->json(['error' => 'Erro ao atualizar o carro'], 500);
        }

        return response()->json($carroAtualizado, 200);
    }



    public function removerCarro(int $id)
    {
        $carro = $this->carroRepository->find($id);

        if (empty($carro)) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        $apagou = $this->carroRepository->delete($id);

        if (!$apagou) {
            throw new \Exception('Erro ao remover a carro', 500);
        }

        return ['msg' => 'carro removida com sucesso!'];
    }
}
