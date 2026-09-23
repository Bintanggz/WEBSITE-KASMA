<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreStudentRequest;
use App\Http\Requests\Bendahara\UpdateStudentRequest;
use App\Models\CashPeriod;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of students with filtering and activation status.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status', 'all');

        $query = User::where('role', 'mahasiswa');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('nim', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            'pending_activation' => $query->whereNull('activated_at')->whereNotNull('activation_token'),
            'activated' => $query->whereNotNull('activated_at'),
            default => null,
        };

        $students = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // Dashboard/header statistics
        $baseStudentQuery = User::where('role', 'mahasiswa');
        $totalStudents = (clone $baseStudentQuery)->count();
        $activeStudents = (clone $baseStudentQuery)->where('is_active', true)->count();
        $inactiveStudents = (clone $baseStudentQuery)->where('is_active', false)->count();
        $pendingActivationCount = (clone $baseStudentQuery)->whereNull('activated_at')->whereNotNull('activation_token')->count();

        return view('bendahara.mahasiswa.index', compact(
            'students',
            'search',
            'status',
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'pendingActivationCount'
        ));
    }

    /**
     * Store a newly created student and generate activation token.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $student = DB::transaction(function () use ($request, $hashedToken) {
            $newStudent = User::create([
                'name' => $request->validated('name'),
                'nim' => $request->validated('nim'),
                'email' => $request->validated('email'),
                'phone_number' => $request->validated('phone_number'),
                'role' => 'mahasiswa', // Strict: treasurer cannot create any other role
                'password' => null,
                'is_active' => true,
                'activation_token' => $hashedToken,
                'activation_expires_at' => now()->addHours(72),
                'activated_at' => null,
            ]);

            // Assign existing cash periods as unpaid dues
            $cashPeriods = CashPeriod::all();
            foreach ($cashPeriods as $period) {
                StudentDue::firstOrCreate(
                    [
                        'user_id' => $newStudent->id,
                        'cash_period_id' => $period->id,
                    ],
                    [
                        'amount' => $period->amount,
                        'status' => 'unpaid',
                    ]
                );
            }

            return $newStudent;
        });

        $activationUrl = route('activation.show', [
            'token' => $plainToken,
            'email' => $student->email,
        ]);

        $waMessage = "Halo {$student->name},\n\nAkun KASMA Anda telah didaftarkan oleh Bendahara Kelas.\nSilakan klik tautan berikut untuk membuat kata sandi dan mengaktifkan akun Anda:\n\n{$activationUrl}\n\nTautan ini berlaku selama 72 jam. Terima kasih!";

        return redirect()->route('bendahara.mahasiswa.index')
            ->with('success', "Mahasiswa {$student->name} ({$student->nim}) berhasil ditambahkan.")
            ->with('new_student_activation', [
                'name' => $student->name,
                'nim' => $student->nim,
                'email' => $student->email,
                'phone_number' => $student->phone_number,
                'activation_url' => $activationUrl,
                'whatsapp_message' => $waMessage,
            ]);
    }

    /**
     * Update the specified student details.
     */
    public function update(UpdateStudentRequest $request, User $mahasiswa): RedirectResponse
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $mahasiswa->update($request->validated());

        return redirect()->route('bendahara.mahasiswa.index')
            ->with('success', "Data mahasiswa {$mahasiswa->name} berhasil diperbarui.");
    }

    /**
     * Toggle student active status (activate/deactivate).
     */
    public function toggleStatus(User $mahasiswa): RedirectResponse
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $newStatus = ! $mahasiswa->is_active;
        $mahasiswa->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Akun mahasiswa {$mahasiswa->name} ({$mahasiswa->nim}) berhasil {$statusText}.");
    }

    /**
     * Resend/regenerate activation token for a student with pending activation.
     */
    public function resendActivation(User $mahasiswa): RedirectResponse
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($mahasiswa->isActivated()) {
            return redirect()->back()
                ->with('error', "Akun mahasiswa {$mahasiswa->name} sudah diaktifkan sebelumnya.");
        }

        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $mahasiswa->update([
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
        ]);

        $activationUrl = route('activation.show', [
            'token' => $plainToken,
            'email' => $mahasiswa->email,
        ]);

        $waMessage = "Halo {$mahasiswa->name},\n\nTautan aktivasi akun KASMA Anda telah diperbarui oleh Bendahara Kelas.\nSilakan klik tautan berikut untuk membuat kata sandi dan mengaktifkan akun Anda:\n\n{$activationUrl}\n\nTautan ini berlaku selama 72 jam. Terima kasih!";

        return redirect()->route('bendahara.mahasiswa.index')
            ->with('success', "Tautan aktivasi baru untuk {$mahasiswa->name} berhasil dibuat.")
            ->with('new_student_activation', [
                'name' => $mahasiswa->name,
                'nim' => $mahasiswa->nim,
                'email' => $mahasiswa->email,
                'phone_number' => $mahasiswa->phone_number,
                'activation_url' => $activationUrl,
                'whatsapp_message' => $waMessage,
            ]);
    }
}
