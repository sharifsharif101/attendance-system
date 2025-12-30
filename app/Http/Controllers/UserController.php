<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // عرض قائمة المستخدمين
    public function index()
    {
        $users = User::with(['roles', 'departments'])->paginate(20);
        return view('users.index', compact('users'));
    }

    // صفحة إضافة مستخدم
    public function create()
    {
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();
        return view('users.create', compact('roles', 'departments'));
    }

    // حفظ مستخدم جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'departments' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        if ($request->departments) {
            $user->departments()->attach($request->departments);
        }

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // صفحة تعديل مستخدم
    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();
        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    // تحديث مستخدم
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'departments' => 'array',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);
        $user->departments()->sync($request->departments ?? []);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    // حذف مستخدم
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف نفسك');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}