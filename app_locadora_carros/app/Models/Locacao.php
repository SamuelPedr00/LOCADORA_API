<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;
    protected $table = 'locacoes';
    protected $fillable = [
        'cliente_id',
        'carro_id',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'carro_id' => 'required|exists:carros,id',
            'data_inicio_periodo' => 'required|date',
            'data_final_previsto_periodo' => 'required|date|after_or_equal:data_inicio_periodo',
            'data_final_realizado_periodo' => 'nullable|date|after_or_equal:data_inicio_periodo',
            'valor_diaria' => 'required|numeric|min:0',
            'km_inicial' => 'required|numeric|min:0',
            'km_final' => 'nullable|numeric|min:km_inicial'
        ];
    }

    public function feedback()
    {
        return [
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'cliente_id.exists' => 'O cliente selecionado não existe.',

            'carro_id.required' => 'O campo carro é obrigatório.',
            'carro_id.exists' => 'O carro selecionado não existe.',

            'data_inicio_periodo.required' => 'A data de início do período é obrigatória.',
            'data_inicio_periodo.date' => 'A data de início do período deve ser uma data válida.',

            'data_final_previsto_periodo.required' => 'A data final prevista é obrigatória.',
            'data_final_previsto_periodo.date' => 'A data final prevista deve ser uma data válida.',
            'data_final_previsto_periodo.after_or_equal' => 'A data final prevista deve ser igual ou posterior à data de início.',

            'data_final_realizado_periodo.date' => 'A data final realizada deve ser uma data válida.',
            'data_final_realizado_periodo.after_or_equal' => 'A data final realizada deve ser igual ou posterior à data de início.',

            'valor_diaria.required' => 'O valor da diária é obrigatório.',
            'valor_diaria.numeric' => 'O valor da diária deve ser numérico.',
            'valor_diaria.min' => 'O valor da diária deve ser maior ou igual a zero.',

            'km_inicial.required' => 'A quilometragem inicial é obrigatória.',
            'km_inicial.numeric' => 'A quilometragem inicial deve ser numérica.',
            'km_inicial.min' => 'A quilometragem inicial deve ser maior ou igual a zero.',

            'km_final.numeric' => 'A quilometragem final deve ser numérica.',
            'km_final.min' => 'A quilometragem final deve ser maior ou igual à quilometragem inicial.'
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
