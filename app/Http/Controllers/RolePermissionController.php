<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles and permissions matrix.
     */
    public function index(Request $request): View
    {
        $roles = Role::with(['permissions', 'users'])->withCount('users')->get();
        $allPermissions = Permission::orderBy('module')->orderBy('name')->get();

        // Group permissions by module
        $permissionsByModule = $allPermissions->groupBy('module');

        // Selected role for permission matrix inspection/editing (default to first or requested)
        $selectedRoleId = $request->query('role_id', $roles->first()?->id);
        $selectedRole = $roles->firstWhere('id', $selectedRoleId) ?? $roles->first();

        // Metrics
        $totalRoles = $roles->count();
        $totalPermissions = $allPermissions->count();
        $totalSystemUsers = User::where('status', 'active')->count();
        $superadminCount = User::whereHas('role', function ($q) {
            $q->where('name', 'superadmin');
        })->count();

        return view('settings.roles.index', compact(
            'roles',
            'permissionsByModule',
            'selectedRole',
            'totalRoles',
            'totalPermissions',
            'totalSystemUsers',
            'superadminCount'
        ));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
            'label' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ], [
            'name.unique' => 'Nama identitas peran (slug) sudah digunakan.',
            'name.alpha_dash' => 'Nama identitas peran hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
            'label.required' => 'Label nama peran wajib diisi.',
        ]);

        $role = Role::create([
            'name' => strtolower($validated['name']),
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('settings.roles.index', ['role_id' => $role->id])
            ->with('status', "Peran baru '{$role->label}' berhasil dibuat.");
    }

    /**
     * Update role details (label & description).
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'label.required' => 'Label nama peran wajib diisi.',
        ]);

        $role->update([
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('settings.roles.index', ['role_id' => $role->id])
            ->with('status', "Informasi peran '{$role->label}' berhasil diperbarui.");
    }

    /**
     * Update permissions matrix for a specific role.
     */
    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Prevent stripping all permissions from superadmin to maintain system access
        if ($role->name === 'superadmin' && empty($validated['permissions'])) {
            return redirect()->route('settings.roles.index', ['role_id' => $role->id])
                ->withErrors(['permissions' => 'Peran Superadmin tidak boleh dikosongkan dari seluruh hak akses.']);
        }

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('settings.roles.index', ['role_id' => $role->id])
            ->with('status', "Hak akses untuk peran '{$role->label}' berhasil diperbarui.");
    }

    /**
     * Remove the specified custom role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Protected system default roles
        if (in_array($role->name, ['superadmin', 'petugas', 'pimpinan'])) {
            return redirect()->route('settings.roles.index')
                ->withErrors(['role' => "Peran bawaan sistem '{$role->label}' tidak dapat dihapus."]);
        }

        // Check if users are assigned to this role
        if ($role->users()->count() > 0) {
            return redirect()->route('settings.roles.index', ['role_id' => $role->id])
                ->withErrors(['role' => "Peran '{$role->label}' masih digunakan oleh {$role->users()->count()} akun pengguna. Ubah peran pengguna terlebih dahulu."]);
        }

        $role->permissions()->detach();
        $label = $role->label;
        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('status', "Peran '{$label}' berhasil dihapus dari sistem.");
    }
}
