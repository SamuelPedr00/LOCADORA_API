<?php

namespace App\Repositories;

interface IMarcaRepository
{
    public function getAll($columns = ['*'], $relations = []);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getModel();
}
