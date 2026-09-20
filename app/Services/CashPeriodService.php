<?php

namespace App\Services;

use App\Models\CashPeriod;
use App\Models\StudentDue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CashPeriodService
{
    /**
     * Create a complete semester set of cash periods and generate student dues.
     *
     * @param array{
     *     academic_year: string,
     *     semester: string,
     *     number_of_weeks: int,
     *     amount: numeric,
     *     start_date: string,
     *     due_date: string,
     *     set_first_active?: bool
     * } $data
     * @return Collection<int, CashPeriod>
     */
    public function createSemesterSet(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $numberOfWeeks = (int) $data['number_of_weeks'];
            $baseStartDate = Carbon::parse($data['start_date']);
            $baseDueDate = Carbon::parse($data['due_date']);
            $amount = $data['amount'];
            $setFirstActive = ! empty($data['set_first_active']);

            // Only generate obligations for active students
            $activeStudents = User::where('role', 'mahasiswa')
                ->where('is_active', true)
                ->get();

            if ($setFirstActive) {
                CashPeriod::where('is_active', true)->update(['is_active' => false]);
            }

            $createdPeriods = collect();

            for ($w = 1; $w <= $numberOfWeeks; $w++) {
                $weekStartDate = $baseStartDate->copy()->addWeeks($w - 1);
                $weekDueDate = $baseDueDate->copy()->addWeeks($w - 1);

                $period = CashPeriod::create([
                    'academic_year' => $data['academic_year'],
                    'semester' => strtolower($data['semester']),
                    'week_number' => $w,
                    'name' => 'Pekan ke-' . $w,
                    'amount' => $amount,
                    'start_date' => $weekStartDate->toDateString(),
                    'due_date' => $weekDueDate->toDateString(),
                    'is_active' => ($w === 1 && $setFirstActive),
                ]);

                // Create student dues for each active student
                foreach ($activeStudents as $student) {
                    StudentDue::firstOrCreate(
                        [
                            'cash_period_id' => $period->id,
                            'user_id' => $student->id,
                        ],
                        [
                            'amount' => $period->amount,
                            'status' => 'unpaid',
                        ]
                    );
                }

                $createdPeriods->push($period);
            }

            return $createdPeriods;
        });
    }

    /**
     * Activate a specific weekly cash period (deactivating all others).
     */
    public function activatePeriod(CashPeriod $period): void
    {
        DB::transaction(function () use ($period) {
            CashPeriod::where('is_active', true)->update(['is_active' => false]);
            $period->update(['is_active' => true]);
        });
    }

    /**
     * Deactivate a specific weekly cash period.
     */
    public function deactivatePeriod(CashPeriod $period): void
    {
        $period->update(['is_active' => false]);
    }
}
