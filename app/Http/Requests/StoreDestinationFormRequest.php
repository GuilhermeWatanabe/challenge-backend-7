<?php

namespace App\Http\Requests;

use App\Rules\DifferentImageRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDestinationFormRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo_1' => 'required|image',
            'photo_2' => ['required', 'image', new DifferentImageRule('photo_1')],
            'meta_description' => 'required|min:1',
            'description' => 'nullable',
            'name' => 'required|min:1',
            'price' => 'required|decimal:0,2|min:1|numeric:strict'
        ];
    }
}
