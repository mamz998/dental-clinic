<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('doctor')->orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'password'        => 'required|min:8|confirmed',
            'role'            => 'required|in:admin,doctor,receptionist',
            'phone'           => 'nullable|string|max:20',
            'is_active'       => 'boolean',
            'specialty'       => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'phone'     => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($data['role'] === 'doctor') {
            Doctor::create([
                'user_id'         => $user->id,
                'specialty'       => $data['specialty'] ?? null,
                'commission_rate' => $data['commission_rate'] ?? 0,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,'.$user->id,
            'password'        => 'nullable|min:8|confirmed',
            'role'            => 'required|in:admin,doctor,receptionist',
            'phone'           => 'nullable|string|max:20',
            'is_active'       => 'boolean',
            'specialty'       => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'phone'     => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if ($data['role'] === 'doctor') {
            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty'       => $data['specialty'] ?? null,
                    'commission_rate' => $data['commission_rate'] ?? 0,
                ]
            );
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم');
    }
}
