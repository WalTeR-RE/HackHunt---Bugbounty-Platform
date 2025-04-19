<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'required|string',
            'severity' => 'required|in:p1,p2,p3,p4,p5',
            'attachments.*' => 'nullable|file|mimes:jpg,png,mp4,pdf|max:10240',
        ];
    }
}
