<?php

namespace App\Repositories;

use App\Models\Carro;
use App\Repositories\ICarroRepository;

class CarroRepository implements ICarroRepository
{
    protected $model;

    public function __construct(Carro $model)
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
        $carro = $this->find($id);

        if (!$carro) {
            return null;
        }

        $carro->update($data);
        return $carro->refresh();
    }

    public function delete($id)
    {
        $carro = $this->find($id);

        if (!$carro) {
            return false;
        }

        return $carro->delete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWithModelos(int $id, string $atributos = '*', ?string $atributos_modelos = null)
    {
        $query = $this->model->selectRaw($atributos);

        if ($atributos_modelos) {
            $query->with('modelos:' . $atributos_modelos);
        } else {
            $query->with('modelos');
        }

        return $query->find($id);
    }
}
