<?php

namespace App\Http\Requests\Bendahara;

use App\Models\StudentDue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCashPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isBendahara() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_due_id' => ['required', 'integer', 'exists:student_dues,id'],
        ];
    }

    /**
     * Additional validation to ensure due is unpaid.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $dueId = $this->input('student_due_id');
            if (! $dueId) {
                return;
            }

            $due = StudentDue::find($dueId);
            if (! $due) {
                return;
            }

            if ($due->isPaid()) {
                $validator->errors()->add('student_due_id', 'Kewajiban kas pekan ini sudah lunas.');
            }
        });
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_due_id' => 'kewajiban kas mahasiswa',
        ];
    }
}
