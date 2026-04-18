<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:180'],
            'subject' => ['nullable', 'string', 'max:200'],
            'body'    => ['required', 'string', 'min:16', 'max:8000'],
            'website' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Lütfen adınızı yazın.',
            'email.required'  => 'Geri dönüş için bir e-posta adresi gerek.',
            'email.email'     => 'E-posta adresi geçerli değil.',
            'body.required'   => 'Mesaj boş olamaz.',
            'body.min'        => 'Mesaj çok kısa — birkaç cümle daha yazar mısın?',
        ];
    }
}
