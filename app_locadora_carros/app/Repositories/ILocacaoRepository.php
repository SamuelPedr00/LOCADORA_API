<?php

namespace App\Repositories;

interface ILocacaoRepository
{
    public function getAll($columns = ['*']);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getModel();
    public function findWithAtributes(int $id, string $atributos = '*');
}
