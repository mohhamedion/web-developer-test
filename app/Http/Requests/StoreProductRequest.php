<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(
                response()->json(['success' =>false,'error'=> $errors], 400)
            );
        }

        parent::failedValidation($validator);
    }

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         return [
                'name'=>'required|max:200',
                'price'=>'required|between:0,99.99',
                'description'=>'required|max:1000',
                'category_id'=>'required|array',
                'category_id.*'=>'int',
                'quantity'=>'required|int|min:1',
                'external_id'=>'required|int|unique:products',

        ];
    }
}
