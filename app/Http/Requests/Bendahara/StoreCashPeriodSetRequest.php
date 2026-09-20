<?php

namespace App\Http\Requests\Bendahara;

use App\Models\CashPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCashPeriodSetRequest extends FormRequest
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
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', 'string', 'in:ganjil,genap'],
            'number_of_weeks' => ['required', 'integer', 'min:1', 'max:24'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
            'set_first_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Prevent duplicate creation of the same academic_year + semester + weeks.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $year = $this->input('academic_year');
            $semester = strtolower($this->input('semester', ''));
            $weeks = (int) $this->input('number_of_weeks', 0);

            if ($year && $semester && $weeks > 0) {
                $existingCount = CashPeriod::where('academic_year', $year)
                    ->where('semester', $semester)
                    ->whereBetween('week_number', [1, $weeks])
                    ->count();

                if ($existingCount > 0) {
                    $validator->errors()->add(
                        'academic_year',
                        "Periode kas untuk Tahun Akademik {$year} semester {$semester} sudah pernah dibuat sebagian atau seluruhnya."
                    );
                }
            }
        });
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'academic_year' => 'tahun akademik',
            'semester' => 'semester',
            'number_of_weeks' => 'jumlah pekan',
            'amount' => 'nominal iuran mingguan',
            'start_date' => 'tanggal mulai pekan 1',
            'due_date' => 'tanggal jatuh tempo pekan 1',
        ];
    }
}
