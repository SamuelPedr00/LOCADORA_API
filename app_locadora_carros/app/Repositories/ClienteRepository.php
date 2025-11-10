<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Repositories\IClienteRepository;

class ClienteRepository implements IClienteRepository
{
    protected $model;

    public function __construct(Cliente $model)
    {
        $this->model = $model;
    }

    public function getAll($columns = ['*'], $relations = [])
    {
        return $this->model->select($columns)->with($relations)->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $cliente = $this->find($id);

        if (!$cliente) {
            return null;
        }

        $cliente->update($data);
        return $cliente->refresh();
    }

    public function delete($id)
    {
        $cliente = $this->find($id);

        if (!$cliente) {
            return false;
        }

        return $cliente->delete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWithLocacoes(int $id, string $atributos = '*', ?string $atributos_locacoes = null)
    {
        $query = $this->model->selectRaw($atributos);

        if ($atributos_locacoes) {
            $query->with('locacoes:' . $atributos_locacoes);
        } else {
            $query->with('locacoes');
        }

        return $query->find($id);
    }
}
