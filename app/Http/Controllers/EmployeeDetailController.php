<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDetailController extends Controller
{
    /**
     * Menampilkan form untuk mengedit detail Karyawan.
     */
    public function edit(User $user)
    {
        // ... (Logika otorisasi) ...
        $user->load('employeeDetail');

        if (!$user->employeeDetail) {
             return redirect()->back()->with('error', 'Detail karyawan belum terinisialisasi. Hubungi Admin.');
        }

        return view('details.employee.edit', compact('user'));
    }

    /**
     * Menyimpan/memperbarui detail Karyawan.
     */
    public function update(Request $request, User $user)
    {
        // ... (Logika otorisasi) ...
        
        $request->validate([
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('employee_details')->ignore($user->id, 'user_id')],
            'division' => 'required|string|max:150',
            'hire_date' => 'required|date',
            'emergency_contact' => 'nullable|string|max:15',
        ]);

        $user->employeeDetail()->update($request->all());

        return redirect()->back()->with('success', 'Detail Karyawan berhasil diperbarui.');
    }
}