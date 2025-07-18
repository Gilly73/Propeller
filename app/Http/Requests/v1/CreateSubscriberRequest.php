<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateSubscriberRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emailAddress' => 'required|string|max:255|email',
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'dateOfBirth' => 'required|date|before:-18 years',
            'marketingConsent' => 'sometimes|boolean',
            'subscriptionLists' => 'sometimes|array',
            'subscriptionLists.*' => 'string|in:London,Edinburgh,Birmingham',
        ];
    }
}
