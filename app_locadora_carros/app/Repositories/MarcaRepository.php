<?php

namespace App\Repositories;

use App\Models\Marca;
use App\Repositories\IMarcaRepository;

class MarcaRepository implements IMarcaRepository
{
    protected $model;

    public function __construct(Marca $model)
    {
        $this->model = $model;
    }

    public function getAll($columns = ['*'], $relations = [])
    {
        return $this->model->select($columns)->with($relations)->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $marca = $this->find($id);
        $marca->update($data);
        return $marca;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findWithModelos(int $id, string $atributos = '*', ?string $atributos_modelos = null)
    {
        $query = $this->model->selectRaw($atributos);

        if ($atributos_modelos) {
            // Exemplo: modelos:id,nome,marca_id
            $query->with('modelos:' . $atributos_modelos);
        } else {
            $query->with('modelos');
        }

        return $query->find($id);
    }
}
