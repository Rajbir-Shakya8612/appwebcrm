<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->get();
        $roles = \App\Models\Role::all();
        
        if (request()->wantsJson()) {
            return response()->json($users);
        }
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'photo' => $request->photo,
            'whatsapp_number' => $request->whatsapp_number,
            'pincode' => $request->pincode,
            'address' => $request->address,
            'location' => $request->location,
            'designation' => $request->designation,
            'date_of_joining' => $request->date_of_joining,
            'status' => $request->status ?? 'active',
            'settings' => $request->settings,
            'target_amount' => $request->target_amount,
            'target_leads' => $request->target_leads,
            'is_active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        
        if (request()->wantsJson()) {
            return response()->json($user);
        }
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $request->is_active ?? $user->is_active,
            'phone' => $request->phone,
            'photo' => $request->photo,
            'whatsapp_number' => $request->whatsapp_number,
            'pincode' => $request->pincode,
            'address' => $request->address,
            'location' => $request->location,
            'designation' => $request->designation,
            'date_of_joining' => $request->date_of_joining,
            'status' => $request->status ?? 'active',
            'settings' => $request->settings,
            'target_amount' => $request->target_amount,
            'target_leads' => $request->target_leads,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}
