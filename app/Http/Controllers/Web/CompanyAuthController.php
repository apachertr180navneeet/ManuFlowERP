<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Session, Hash, Validator, File, Exception;

class CompanyAuthController extends Controller
{
    public function login()
    {
        return view('web.auth.login');
    }

    public function postLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $user = Auth::attempt([
                'username' => $request->username,
                'password' => $request->password,
                'role' => 'user',
                'status' => 'active',
            ]);

            if ($user) {
                return redirect()->route('company.dashboard')->with('success', 'Welcome to your dashboard.');
            }

            return back()->with('error', 'Invalid credentials or account inactive.');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function dashboard()
    {
        $company = Company::find(Auth::user()->company_id);
        return view('web.dashboard', compact('company'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('web.auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required|digits:10|unique:users,phone,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'avatar' => 'sometimes|image|mimes:jpeg,jpg,png|max:5000',
            ]);

            if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/user/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $user->avatar = $folder . $filename;
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name . ' ' . $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'An error occurred');
        }
    }

    public function changePassword()
    {
        return view('web.auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            if (!Hash::check($request->old_password, Auth::user()->password)) {
                return back()->with('error', 'Old password does not match!');
            }

            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Password changed successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('company.login')->withSuccess('Logout Successful!');
    }
}
