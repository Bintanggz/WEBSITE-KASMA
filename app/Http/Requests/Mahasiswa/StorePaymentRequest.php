<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\StudentDue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isMahasiswa() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_due_ids' => ['required', 'array', 'min:1'],
            'student_due_ids.*' => ['integer', 'exists:student_dues,id'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,qris'],
            'proof_file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Prepare inputs for validation (handling single student_due_id if passed).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('student_due_id') && ! $this->has('student_due_ids')) {
            $this->merge([
                'student_due_ids' => [$this->input('student_due_id')],
            ]);
        }
    }

    /**
     * Additional validation to ensure dues belong to user, are unpaid, and have no pending payment.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $dueIds = $this->input('student_due_ids', []);
            if (empty($dueIds) || ! is_array($dueIds)) {
                return;
            }

            $userId = $this->user()->id;
            $dues = StudentDue::with(['pendingPayment', 'approvedPayment', 'cashPeriod'])->whereIn('id', $dueIds)->get();

            if ($dues->count() !== count(array_unique($dueIds))) {
                $validator->errors()->add('student_due_ids', 'Sebagian pekan iuran yang dipilih tidak ditemukan.');
                return;
            }

            foreach ($dues as $due) {
                if ($due->user_id !== $userId) {
                    $validator->errors()->add('student_due_ids', 'Pekan iuran yang dipilih bukan milik akun Anda.');
                    return;
                }

                if ($due->isPaid() || $due->approvedPayment !== null) {
                    $validator->errors()->add('student_due_ids', 'Pekan iuran ' . ($due->cashPeriod->name ?? '') . ' sudah lunas.');
                    return;
                }

                if ($due->pendingPayment !== null) {
                    $validator->errors()->add('student_due_ids', 'Pekan iuran ' . ($due->cashPeriod->name ?? '') . ' masih menunggu verifikasi bendahara.');
                    return;
                }
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
            'student_due_ids' => 'pekan iuran',
            'payment_method' => 'metode pembayaran',
            'proof_file' => 'bukti transfer',
        ];
    }
}
