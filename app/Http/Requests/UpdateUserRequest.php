<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->get('id');

        return [
            'id' => 'required|exists:users,id',
            'name' => 'min:3|max:100',
            'email' => "email|min:3|max:100|unique:users,email,{$id}",
        ];
    }
}
