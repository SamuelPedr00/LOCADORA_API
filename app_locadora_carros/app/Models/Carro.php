<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carro extends Model
{
    use HasFactory;
    protected $fillable = ['modelo_id', 'placa', 'disponivel', 'km'];

    public function rules()
    {
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required',
            'disponivel' => 'required',
            'km' => 'required'
        ];
    }
    public function feedback()
    {
        return [
            'modelo_id.required' => 'O campo modelo é obrigatório.',
            'modelo_id.exists' => 'O modelo selecionado não existe.',

            'placa.required' => 'A placa é obrigatória.',
            'placa.string' => 'A placa deve ser um texto.',
            'placa.min' => 'A placa deve ter no mínimo 7 caracteres.',
            'placa.max' => 'A placa deve ter no máximo 8 caracteres.',
            'placa.unique' => 'Esta placa já está cadastrada.',

            'disponivel.required' => 'Informe se o carro está disponível.',
            'disponivel.boolean' => 'O campo disponível deve ser verdadeiro ou falso.',

            'km.required' => 'Informe a quilometragem do veículo.',
            'km.numeric' => 'A quilometragem deve ser um número.',
            'km.min' => 'A quilometragem não pode ser negativa.'
        ];
    }
    public function modelos()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id');
    }

    public function locacoes()
    {
        return $this->hasMany(Locacao::class);
    }
}
