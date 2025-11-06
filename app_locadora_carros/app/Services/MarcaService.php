<?php

namespace App\Services;

use App\Repositories\IMarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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

    public function atualizarMarca(Request $request, int $id)
    {
        $marca = $this->marcaRepository->find($id);

        if (!$marca) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        // Validação dinâmica para PATCH ou completa para PUT
        $regras = $request->method() === 'PATCH'
            ? collect($marca->rules())->only(array_keys($request->all()))->toArray()
            : $marca->rules();

        $request->validate($regras, $marca->feedback());

        $dados = [];

        // Atualiza imagem, se enviada
        if ($request->hasFile('imagem')) {
            if ($marca->imagem) {
                Storage::disk('public')->delete($marca->imagem);
            }

            $dados['imagem'] = $request->file('imagem')->store('imagens', 'public');
        }

        // Atualiza nome, se enviado
        if ($request->has('nome')) {
            $dados['nome'] = $request->nome;
        }

        if (empty($dados)) {
            throw new \Exception('Nenhum dado foi enviado para atualização', 400);
        }

        $marcaAtualizada = $this->marcaRepository->update($id, $dados);

        if (!$marcaAtualizada) {
            throw new \Exception('Erro ao atualizar a marca', 500);
        }

        return $marcaAtualizada;
    }


    public function removerMarca(int $id)
    {
        $marca = $this->marcaRepository->find($id);

        if (empty($marca)) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        if ($marca->imagem) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $apagou = $this->marcaRepository->delete($id);

        if (!$apagou) {
            throw new \Exception('Erro ao remover a marca', 500);
        }

        return ['msg' => 'Marca removida com sucesso!'];
    }
}
