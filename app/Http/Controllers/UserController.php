<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'department']);
        $remark = ActivityType::LIST->generateRemark('Users');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
            $remark = ActivityType::SEARCH->generateRemark('User', "Keyword: {$search}");
        }

        // Log Activity
        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => $remark
        ]);

        $users = $query->paginate(10);
        
        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean'
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'is_active' => $request->is_active ?? true,
            'created_by' => Auth::id(),
        ]);

        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::CREATE->generateRemark('User', $user->username)
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['role', 'department'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::READ->generateRemark('User', $user->username)
        ]);

        return response()->json([
            'message' => 'User details',
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean'
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'is_active' => $request->is_active ?? $user->is_active,
            'updated_by' => Auth::id(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::UPDATE->generateRemark('User', $user->username)
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update(['deleted_by' => Auth::id()]);
        $user->delete();

        LogActivity::create([
            'user_id' => Auth::id(),
            'remark' => ActivityType::DELETE->generateRemark('User', $user->username)
        ]);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
