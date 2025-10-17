<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SecureBaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validación adicional de seguridad
            $this->validateNoMaliciousContent($validator);
            $this->validateFileUploads($validator);
        });
    }

    /**
     * Validar que no haya contenido malicioso
     */
    protected function validateNoMaliciousContent(Validator $validator): void
    {
        $maliciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/data:text\/html/i',
        ];

        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($maliciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $validator->errors()->add($key, 'El campo contiene contenido no permitido.');
                        break;
                    }
                }
            }
        }
    }

    /**
     * Validar archivos subidos
     */
    protected function validateFileUploads(Validator $validator): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        foreach ($this->allFiles() as $key => $file) {
            if ($file && !in_array($file->getMimeType(), $allowedMimeTypes)) {
                $validator->errors()->add($key, 'Tipo de archivo no permitido.');
            }
            
            // Validar tamaño máximo (10MB)
            if ($file && $file->getSize() > 10485760) {
                $validator->errors()->add($key, 'El archivo es demasiado grande (máximo 10MB).');
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Sanitizar entrada
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();
        
        // Sanitizar strings
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = strip_tags($value, '<p><br><strong><em><ul><ol><li>');
                $value = trim($value);
            }
        });
        
        $this->replace($input);
    }
}
