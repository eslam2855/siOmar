<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;

class ReservationListRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'filter' => 'nullable|string|in:upcoming,current,finished',
            'status' => 'nullable|string|in:' . implode(',', [
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_ACTIVE,
                Reservation::STATUS_COMPLETED,
                Reservation::STATUS_CANCELLED
            ]),
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to' => 'nullable|date|date_format:Y-m-d|after_or_equal:date_from',
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|string|in:created_at,check_in_date,check_out_date,total_amount,status',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'filter.in' => 'Filter must be one of: upcoming, current, finished',
            'status.in' => 'Status must be one of: pending, confirmed, active, completed, cancelled',
            'date_from.date_format' => 'Date from must be in Y-m-d format',
            'date_to.date_format' => 'Date to must be in Y-m-d format',
            'date_to.after_or_equal' => 'Date to must be after or equal to date from',
            'sort_by.in' => 'Sort by must be one of: created_at, check_in_date, check_out_date, total_amount, status',
            'sort_order.in' => 'Sort order must be either asc or desc',
            'per_page.min' => 'Per page must be at least 1',
            'per_page.max' => 'Per page cannot exceed 50',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->search ? trim($this->search) : null,
            'sort_by' => $this->sort_by ?: 'created_at',
            'sort_order' => $this->sort_order ?: 'desc',
            'per_page' => $this->per_page ? (int) $this->per_page : 10,
        ]);
    }
}
