<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules()
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:marcas,nome,' . $this->id,
            'imagem' => 'required|file|mimes:png',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'

        ];
    }


    public function feedback()
    {
        return [
            'marca_id.exists' => 'A marca selecionada não existe no sistema',

            'nome.required' => 'O nome do modelo é obrigatório',
            'nome.unique' => 'Este nome de modelo já está cadastrado',

            'imagem.required' => 'A imagem do modelo é obrigatória',
            'imagem.file' => 'O arquivo enviado não é válido',
            'imagem.mimes' => 'A imagem deve estar no formato PNG',

            'numero_portas.required' => 'O número de portas é obrigatório',
            'numero_portas.integer' => 'O número de portas deve ser um valor inteiro',
            'numero_portas.digits_between' => 'O número de portas deve ter entre 1 e 5 dígitos',

            'lugares.required' => 'O número de lugares é obrigatório',
            'lugares.integer' => 'O número de lugares deve ser um valor inteiro',
            'lugares.digits_between' => 'O número de lugares deve estar entre 1 e 20',

            'air_bag.required' => 'A informação sobre air bag é obrigatória',
            'air_bag.boolean' => 'O campo air bag deve ser verdadeiro ou falso',

            'abs.required' => 'A informação sobre ABS é obrigatória',
            'abs.boolean' => 'O campo ABS deve ser verdadeiro ou falso'
        ];
    }

    // Relacionamento com a marca
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
