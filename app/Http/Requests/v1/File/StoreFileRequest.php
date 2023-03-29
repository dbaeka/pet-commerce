<?php

namespace App\Http\Requests\v1\File;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="StoreFileRequest",
 *    required={"file"},
 *    @OA\Property(
 *     property="file",
 *     type="string",
 *     format="binary",
 *     description="File to upload",
 *    ),
 * )
 */
class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file']
        ];
    }
}
