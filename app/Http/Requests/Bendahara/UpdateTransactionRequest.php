<?php

namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'receipt_file' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipe transaksi',
            'amount' => 'nominal transaksi',
            'category' => 'kategori',
            'description' => 'keterangan transaksi',
            'transaction_date' => 'tanggal transaksi',
            'receipt_file' => 'bukti nota/kuitansi',
        ];
    }
}
