<?php

namespace App\Repositories;

use App\Models\Locacao;
use App\Repositories\ILocacaoRepository;

class LocacaoRepository implements ILocacaoRepository
{
    protected $model;

    public function __construct(Locacao $model)
    {
        $this->model = $model;
    }

    public function getAll($columns = ['*'])
    {
        return $this->model->select($columns)->get();
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
        $marca = $this->find($id);

        if (!$marca) {
            return null;
        }

        $marca->update($data);
        return $marca->refresh();
    }

    public function delete($id)
    {
        $marca = $this->find($id);

        if (!$marca) {
            return false;
        }

        return $marca->delete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWithAtributes(int $id, string $atributos = '*')
    {
        $query = $this->model->selectRaw($atributos);

        return $query->find($id);
    }
}
