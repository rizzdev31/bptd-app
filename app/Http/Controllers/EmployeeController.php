<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees (Master Pegawai & Penerima ATK).
     */
    public function index(Request $request): View
    {
        $query = Recipient::with(['workUnit', 'user.role']);

        // Search by keyword (Name, NIP, or Position)
        if ($keyword = $request->filled('keyword') ? trim($request->keyword) : null) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nip', 'like', "%{$keyword}%")
                  ->orWhere('position', 'like', "%{$keyword}%");
            });
        }

        // Filter by Work Unit
        if ($request->filled('work_unit_id')) {
            $query->where('work_unit_id', $request->work_unit_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Summary Metric Cards
        $totalEmployees = Recipient::count();
        $activeEmployees = Recipient::where('status', 'active')->count();
        $systemUsers = User::where('status', 'active')->count();
        $totalUnits = WorkUnit::where('status', 'active')->count();

        // Paginated results
        $employees = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Dropdown references
        $workUnits = WorkUnit::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('pegawai.index', compact(
            'employees',
            'workUnits',
            'roles',
            'totalEmployees',
            'activeEmployees',
            'systemUsers',
            'totalUnits'
        ));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:recipients,nip'],
            'name' => ['required', 'string', 'max:150'],
            'work_unit_id' => ['required', 'exists:work_units,id'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100', 'unique:recipients,email'],
            'status' => ['required', 'in:active,inactive'],
            'create_user' => ['nullable', 'boolean'],
            'username' => ['required_if:create_user,1', 'nullable', 'string', 'max:50', 'unique:users,username'],
            'role_id' => ['required_if:create_user,1', 'nullable', 'exists:roles,id'],
            'password' => ['required_if:create_user,1', 'nullable', 'string', 'min:6'],
            'description' => ['nullable', 'string'],
        ], [
            'nip.unique' => 'NIP sudah terdaftar dalam sistem.',
            'email.unique' => 'Email sudah digunakan pegawai lain.',
            'username.unique' => 'Username akun login sudah digunakan.',
            'password.min' => 'Password akun minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $userId = null;

            // Jika dibuatkan akun sistem login
            if ($request->boolean('create_user')) {
                $user = User::create([
                    'username' => $validated['username'] ?? $validated['nip'],
                    'nip' => $validated['nip'],
                    'name' => $validated['name'],
                    'email' => $validated['email'] ?? ($validated['username'] . '@bptd-jatim.local'),
                    'password' => Hash::make($request->password),
                    'role_id' => $validated['role_id'],
                    'work_unit_id' => $validated['work_unit_id'],
                    'phone' => $validated['phone'],
                    'status' => $validated['status'],
                ]);
                $userId = $user->id;
            }

            Recipient::create([
                'nip' => $validated['nip'],
                'name' => $validated['name'],
                'work_unit_id' => $validated['work_unit_id'],
                'position' => $validated['position'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'status' => $validated['status'],
                'user_id' => $userId,
                'description' => $validated['description'] ?? null,
            ]);
        });

        return redirect()->route('pegawai.index')->with('status', 'Pegawai baru berhasil ditambahkan.');
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Recipient $recipient): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('recipients', 'nip')->ignore($recipient->id)],
            'name' => ['required', 'string', 'max:150'],
            'work_unit_id' => ['required', 'exists:work_units,id'],
            'position' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('recipients', 'email')->ignore($recipient->id)],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
            // Role update jika terhubung akun
            'role_id' => ['nullable', 'exists:roles,id'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($request, $validated, $recipient) {
            $recipient->update([
                'nip' => $validated['nip'],
                'name' => $validated['name'],
                'work_unit_id' => $validated['work_unit_id'],
                'position' => $validated['position'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            // Sinkronkan akun user jika terhubung
            if ($recipient->user) {
                $userUpdates = [
                    'nip' => $validated['nip'],
                    'name' => $validated['name'],
                    'work_unit_id' => $validated['work_unit_id'],
                    'phone' => $validated['phone'],
                    'status' => $validated['status'],
                ];

                if (!empty($validated['role_id'])) {
                    $userUpdates['role_id'] = $validated['role_id'];
                }

                if (!empty($validated['new_password'])) {
                    $userUpdates['password'] = Hash::make($validated['new_password']);
                }

                $recipient->user->update($userUpdates);
            }
        });

        return redirect()->route('pegawai.index')->with('status', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified employee (or toggle inactive status).
     */
    public function destroy(Recipient $recipient): RedirectResponse
    {
        // Toggle status aktif/nonaktif untuk menjaga integritas riwayat
        $newStatus = $recipient->status === 'active' ? 'inactive' : 'active';
        $recipient->update(['status' => $newStatus]);

        if ($recipient->user) {
            $recipient->user->update(['status' => $newStatus]);
        }

        $message = $newStatus === 'inactive' ? 'Pegawai telah dinonaktifkan.' : 'Pegawai telah diaktifkan kembali.';
        return redirect()->route('pegawai.index')->with('status', $message);
    }
}
