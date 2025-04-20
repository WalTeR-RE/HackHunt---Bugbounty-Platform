<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:New,Triaged,Duplicate,Informative,Resolved,N/A',
            'points' => 'nullable|integer|min:0',
            'bounty' => 'nullable|integer|min:0',
        ];
    }
}
