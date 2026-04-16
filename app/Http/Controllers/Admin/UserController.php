<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['links', 'advertisements'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->username = $request->username;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$user->username} updated successfully.",
                'user' => $user
            ]);
        }

        return back()->with('success', "User updated successfully.");
    }

    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => "You cannot delete yourself."], 403);
            }
            return back()->with('error', "You cannot delete yourself.");
        }

        $username = $user->username;
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "User {$username} deleted successfully."]);
        }

        return back()->with('success', "User deleted successfully.");
    }
}
