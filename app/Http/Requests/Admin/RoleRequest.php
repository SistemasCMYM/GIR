<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $roleId = $this->route('role') ? $this->route('role')->id : null;
        
        return [
            'nombre' => [
                'required', 
                'string', 
                'max:100',
                Rule::unique('roles', 'nombre')->ignore($roleId)
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'permisos' => ['required', 'array', 'min:1'],
            'permisos.*' => ['string', 'max:100'],
            'nivel_acceso' => [
                'required', 
                'integer', 
                'min:1', 
                'max:10'
            ],
            'activo' => ['boolean'],
            'es_sistema' => ['boolean'],
            'modulos_permitidos' => ['nullable', 'array'],
            'modulos_permitidos.*' => ['string'],
            'restricciones' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            'permisos.required' => 'Debe seleccionar al menos un permiso.',
            'permisos.min' => 'Debe seleccionar al menos un permiso.',
            'nivel_acceso.required' => 'El nivel de acceso es obligatorio.',
            'nivel_acceso.min' => 'El nivel de acceso debe ser al menos 1.',
            'nivel_acceso.max' => 'El nivel de acceso no puede ser mayor a 10.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Valores por defecto
        $this->merge([
            'activo' => $this->has('activo') ? filter_var($this->activo, FILTER_VALIDATE_BOOLEAN) : true,
            'es_sistema' => $this->has('es_sistema') ? filter_var($this->es_sistema, FILTER_VALIDATE_BOOLEAN) : false,
        ]);

        // Limpiar arrays vacíos
        if ($this->has('permisos') && empty($this->permisos)) {
            $this->merge(['permisos' => []]);
        }
        
        if ($this->has('modulos_permitidos') && empty($this->modulos_permitidos)) {
            $this->merge(['modulos_permitidos' => []]);
        }
    }
}
