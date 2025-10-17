<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tipo_reporte' => [
                'required',
                'string',
                Rule::in(['empresas', 'usuarios', 'hallazgos', 'psicosocial', 'actividad', 'completo'])
            ],
            'formato' => [
                'required',
                'string',
                Rule::in(['pdf', 'excel', 'csv'])
            ],
            'fecha_inicio' => ['nullable', 'date', 'before_or_equal:fecha_fin'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'empresa_id' => ['nullable', 'string', 'exists:empresas,id'],
            'usuario_id' => ['nullable', 'string', 'exists:usuarios,id'],
            'estado' => ['nullable', 'string'],
            'incluir_graficos' => ['boolean'],
            'incluir_detalles' => ['boolean'],
            'incluir_anexos' => ['boolean'],
            'filtros_adicionales' => ['nullable', 'array'],
            'columnas_personalizadas' => ['nullable', 'array'],
            'columnas_personalizadas.*' => ['string']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tipo_reporte.required' => 'El tipo de reporte es obligatorio.',
            'tipo_reporte.in' => 'El tipo de reporte seleccionado no es válido.',
            'formato.required' => 'El formato de exportación es obligatorio.',
            'formato.in' => 'El formato seleccionado no es válido.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de fin.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'empresa_id.exists' => 'La empresa seleccionada no existe.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Valores por defecto para checkboxes
        $this->merge([
            'incluir_graficos' => $this->has('incluir_graficos') ? filter_var($this->incluir_graficos, FILTER_VALIDATE_BOOLEAN) : false,
            'incluir_detalles' => $this->has('incluir_detalles') ? filter_var($this->incluir_detalles, FILTER_VALIDATE_BOOLEAN) : true,
            'incluir_anexos' => $this->has('incluir_anexos') ? filter_var($this->incluir_anexos, FILTER_VALIDATE_BOOLEAN) : false,
        ]);

        // Si no se especifican fechas, usar el último mes
        if (!$this->has('fecha_inicio') && !$this->has('fecha_fin')) {
            $this->merge([
                'fecha_inicio' => now()->subMonth()->format('Y-m-d'),
                'fecha_fin' => now()->format('Y-m-d')
            ]);
        }
    }
}
