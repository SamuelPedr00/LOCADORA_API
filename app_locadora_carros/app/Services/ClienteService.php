<?php

namespace App\Services;

use App\Repositories\IClienteRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class ClienteService
{
    protected $clienteRepository;

    public function __construct(IClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    public function listarClientes(Request $request)
    {

        $columns = ['*'];
        $relations = [];

        // Aplica filtros (essas regras pertencem à camada de negócio)
        if ($request->has('atributos')) {
            $columns = explode(',', $request->atributos);
        }

        if ($request->has('atributos_locacoes')) {
            $atributos_locacoes = $request->atributos_locacoes;
            $relations = ['locacoes:id,' . $atributos_locacoes];
        } else {
            $relations = ['locacoes'];
        }


        return $this->clienteRepository->getAll($columns, $relations);
    }

    public function criarCliente(Request $request)
    {
        // Aplicar a validação
        $request->validate($this->clienteRepository->getModel()->rules(), $this->clienteRepository->getModel()->feedback());

        // Cria a cliente
        return $this->clienteRepository->create([
            'nome' => $request->nome
        ]);
    }

    public function buscarCliente(Request $request, int $id)
    {
        // Lê os atributos solicitados
        $atributos = $request->atributos ?? '*';
        $atributos_locacoes = $request->atributos_locacoes ?? null;

        // Busca no repositório
        $cliente = $this->clienteRepository->findWithLocacoes($id, $atributos, $atributos_locacoes);

        if (!$cliente) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        return $cliente;
    }

    public function atualizarCliente(Request $request, int $id)
    {
        $cliente = $this->clienteRepository->find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Carro não encontrado'], 404);
        }

        // Define as regras (dinâmicas no PATCH)
        $regras = $request->method() === 'PATCH'
            ? collect($cliente->rules())->only(array_keys($request->all()))->toArray()
            : $cliente->rules();

        // Laravel lança 422 automaticamente se houver erro
        $validated = $request->validate($regras, $cliente->feedback());

        // Atualiza apenas os campos enviados
        $dados = $request->only(['nome']);

        if (empty($dados)) {
            return response()->json(['error' => 'Nenhum dado foi enviado para atualização'], 400);
        }

        $clienteAtualizado = $this->clienteRepository->update($id, $dados);

        if (!$clienteAtualizado) {
            return response()->json(['error' => 'Erro ao atualizar o carro'], 500);
        }

        return response()->json($clienteAtualizado, 200);
    }



    public function removerCliente(int $id)
    {
        $cliente = $this->clienteRepository->find($id);

        if (empty($cliente)) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        $apagou = $this->clienteRepository->delete($id);

        if (!$apagou) {
            throw new \Exception('Erro ao remover a cliente', 500);
        }

        return ['msg' => 'cliente removida com sucesso!'];
    }
}
