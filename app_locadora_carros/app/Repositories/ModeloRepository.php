<?php

namespace App\Repositories;

use App\Models\Modelo;
use App\Repositories\IModeloRepository;

class ModeloRepository implements IModeloRepository
{
    protected $model;

    public function __construct(Modelo $model)
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
        $modelo = $this->find($id);

        if (!$modelo) {
            return null;
        }

        $modelo->update($data);
        return $modelo->refresh();
    }

    public function delete($id)
    {
        $modelo = $this->find($id);

        if (!$modelo) {
            return false;
        }

        return $modelo->delete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWithMarca(int $id, string $atributos = '*', ?string $atributos_marca = null)
    {
        $query = $this->model->selectRaw($atributos);

        if ($atributos_marca) {
            // Exemplo: marca:id,nome,marca_id
            $query->with('marca:' . $atributos_marca);
        } else {
            $query->with('marca');
        }

        return $query->find($id);
    }
}
