<?php

namespace App\Services;

use App\Repositories\IModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ModeloService
{
    protected $modeloRepository;

    public function __construct(IModeloRepository $modeloRepository)
    {
        $this->modeloRepository = $modeloRepository;
    }

    public function listarModelos(Request $request)
    {

        $columns = ['*'];
        $relations = [];

        // Aplica filtros (essas regras pertencem à camada de negócio)
        if ($request->has('atributos')) {
            $columns = explode(',', $request->atributos);
        }

        if ($request->has('atributos_marca')) {
            $atributos_marca = $request->atributos_marca;
            $relations = ['marca:id,' . $atributos_marca];
        } else {
            $relations = ['marca'];
        }

        return $this->modeloRepository->getAll($columns, $relations);
    }

    public function criarModelo(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->modeloRepository->getModel()->rules(), $this->modeloRepository->getModel()->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagem/modelos', 'public');

        // Cria a marca
        return $this->modeloRepository->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
            'imagem' => $imagem_urn
        ]);
    }

    public function buscarModelo(Request $request, int $id)
    {
        // Lê os atributos solicitados
        $atributos = $request->atributos ?? '*';
        $atributos_marca = $request->atributos_marca ?? null;

        // Busca no repositório
        $modelo = $this->modeloRepository->findWithMarca($id, $atributos, $atributos_marca);

        if (!$modelo) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        return $modelo;
    }

    public function atualizarModelo(Request $request, int $id)
    {
        $modelo = $this->modeloRepository->find($id);

        if (!$modelo) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        // Validação dinâmica para PATCH ou completa para PUT
        $regras = $request->method() === 'PATCH'
            ? collect($modelo->rules())->only(array_keys($request->all()))->toArray()
            : $modelo->rules();

        $request->validate($regras, $modelo->feedback());

        $dados = [];

        // Atualiza imagem, se enviada
        if ($request->hasFile('imagem')) {
            if ($modelo->imagem) {
                Storage::disk('public')->delete($modelo->imagem);
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

        $modeloAtualizada = $this->modeloRepository->update($id, $dados);

        if (!$modeloAtualizada) {
            throw new \Exception('Erro ao atualizar a modelo', 500);
        }

        return $modeloAtualizada;
    }


    public function removerModelo(int $id)
    {
        $modelo = $this->modeloRepository->find($id);

        if (empty($modelo)) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        if ($modelo->imagem) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $apagou = $this->modeloRepository->delete($id);

        if (!$apagou) {
            throw new \Exception('Erro ao remover a modelo', 500);
        }

        return ['msg' => 'modelo removida com sucesso!'];
    }
}
