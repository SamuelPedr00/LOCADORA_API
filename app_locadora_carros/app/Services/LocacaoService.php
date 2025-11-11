<?php

namespace App\Services;

use App\Repositories\ILocacaoRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LocacaoService
{
    protected $locacaoRepository;

    public function __construct(ILocacaoRepository $locacaoRepository)
    {
        $this->locacaoRepository = $locacaoRepository;
    }

    public function listarLocacoes(Request $request)
    {

        $columns = ['*'];

        // Aplica filtros (essas regras pertencem à camada de negócio)
        if ($request->has('atributos')) {
            $columns = explode(',', $request->atributos);
        }

        return $this->locacaoRepository->getAll($columns);
    }

    public function criarLocacao(Request $request)
    {
        $model = $this->locacaoRepository->getModel();

        // Validação usando as regras do modelo
        $validated = $request->validate($model->rules(), $model->feedback());

        // Criação do registro com dados validados
        $locacao = $this->locacaoRepository->create([
            'cliente_id' => $validated['cliente_id'],
            'carro_id' => $validated['carro_id'],
            'data_inicio_periodo' => Carbon::parse($validated['data_inicio_periodo'])->format('Y-m-d H:i:s'),
            'data_final_previsto_periodo' => Carbon::parse($validated['data_final_previsto_periodo'])->format('Y-m-d H:i:s'),
            'data_final_realizado_periodo' => isset($validated['data_final_realizado_periodo'])
                ? Carbon::parse($validated['data_final_realizado_periodo'])->format('Y-m-d H:i:s')
                : Carbon::parse($validated['data_final_previsto_periodo'])->format('Y-m-d H:i:s'),
            'valor_diaria' => (float) $validated['valor_diaria'],
            'km_inicial' => (int) $validated['km_inicial'],
            'km_final' => $validated['km_final'] ? (int) $validated['km_final'] : null,
        ]);

        return $locacao;
    }

    public function buscarLocacao(Request $request, int $id)
    {
        // Lê os atributos solicitados
        $atributos = $request->atributos ?? '*';

        // Busca no repositório
        $locacao = $this->locacaoRepository->findWithAtributes($id, $atributos);

        if (!$locacao) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        return $locacao;
    }

    public function atualizarLocacao(Request $request, int $id)
    {
        $locacao = $this->locacaoRepository->find($id);

        if (!$locacao) {
            return response()->json(['error' => 'Locacao não encontrado'], 404);
        }

        // Define as regras (dinâmicas no PATCH)
        $regras = $request->method() === 'PATCH'
            ? collect($locacao->rules())->only(array_keys($request->all()))->toArray()
            : $locacao->rules();

        $validated = $request->validate($regras, $locacao->feedback());

        // Atualiza apenas os campos enviados
        $dados = $request->only([
            'cliente_id',
            'carro_id',
            'data_inicio_periodo',
            'data_final_previsto_periodo',
            'data_final_realizado_periodo',
            'valor_diaria',
            'km_inicial',
            'km_final'
        ]);

        if (empty($dados)) {
            return response()->json(['error' => 'Nenhum dado foi enviado para atualização'], 400);
        }

        $locacaoAtualizado = $this->locacaoRepository->update($id, $dados);

        if (!$locacaoAtualizado) {
            return response()->json(['error' => 'Erro ao atualizar o carro'], 500);
        }

        return $locacaoAtualizado;
    }



    public function removerLocacao(int $id)
    {
        $locacao = $this->locacaoRepository->find($id);

        if (empty($locacao)) {
            throw new \Exception('Pesquisa não encontrada', 404);
        }

        $apagou = $this->locacaoRepository->delete($id);

        if (!$apagou) {
            throw new \Exception('Erro ao remover a locacao', 500);
        }

        return ['msg' => 'locacao removida com sucesso!'];
    }
}
