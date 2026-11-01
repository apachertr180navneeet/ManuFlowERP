<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash, Validator, Session, File, Exception;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    
    public function index()
    {
        try{
            if(Auth::user()) {
                $user = Auth::user();
                if($user->role == "admin") {
                    return redirect()->route('admin.dashboard');
                }else{
                    return back()->with("error","Opps! You do not have access this");
                }
            }else{
                return redirect()->route('admin.login');
            }

        }
        catch(\Throwable $e){
            Log::error('AdminAuthController::index error: ' . $e->getMessage());
            return back()->with("error", 'An error occurred');
        }
    }

    

    public function login()
    {
        return view("admin.auth.login");
    }

    public function postLogin(Request $request)
    {
        try{
            $request->validate([
                "email" => "required",
                "password" => "required",
            ]);
            $user = User::where('role','admin')->where('email',$request->email)->first();
            if($user){
                $credentials = $request->only("email", "password");
                if(Auth::attempt($credentials))
                {
                    return redirect()->route("admin.dashboard")->with("success", "Welcome to your dashboard.");
                }
                return back()->with("error","Invalid credentials");
            }else{
                return back()->with("error","Invalid credentials");
            }

        }
        catch(\Throwable $e){
            Log::error('AdminAuthController::postLogin error: ' . $e->getMessage());
            return back()->with("error", 'An error occurred');
        }
    }

    public function changePassword()
    {
        return view("admin.auth.change-password");
    }

    public function updatePassword(Request $request)
    {
        try{
            $request->validate([
                "old_password" => "required",
                "new_password" => "required|confirmed",
            ]);
            #Match The Old Password
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return back()->with("error", "Old Password Doesn't match!");
            }
            #Update the new Password
            User::whereId(auth()->user()->id)->update([
                "password" => Hash::make($request->new_password),
            ]);
            return back()->with("success", "Password changed successfully!");
        }
        catch(\Throwable $e){
            Log::error('AdminAuthController::updatePassword error: ' . $e->getMessage());
            return back()->with("error", 'An error occurred');
        }
    }

    

    public function logout()
    {
        try{
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route("admin.login")->withSuccess('Logout Successful!');
        }
        catch(\Throwable $e){
            Log::error('AdminAuthController::logout error: ' . $e->getMessage());
            return back()->with("error", 'An error occurred');
        }
    }

    public function adminProfile()
    {
        try{
            $user = Auth::user();
            return view("admin.auth.profile", compact("user"));

        }
        catch(\Throwable $e){
            Log::error('AdminAuthController::adminProfile error: ' . $e->getMessage());
            return back()->with("error", 'An error occurred');
        }
    }

    public function updateAdminProfile(Request $request)
    {
        try
        {
            $user = Auth::user();
            $data = $request->only(['first_name', 'last_name', 'phone', 'email']);
            $validator = Validator::make($data,[
                "first_name" => "required",
                "last_name" => "required",
                "phone" => "required|digits:10|unique:users,phone," .$user->id,
                "email" => "required|email|unique:users,email," . $user->id,
                "avatar" => "sometimes|image|mimes:jpeg,jpg,png|max:5000"
            ]);
            
            if($validator->fails()) {
                return redirect()->back()->withInput($request->all())->withErrors($validator->errors());
            }
            
            if($request->file("avatar")) {
                $file = $request->file("avatar");
                $filename = time() . $file->getClientOriginalName();
                $folder = "uploads/user/";
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $file->move($path, $filename);
                $user->avatar = $folder . $filename;
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name . " " . $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->save();
            return redirect()->back()->with("success", "Profile update successfully!");
        }
        catch (\Throwable $e) {
            Log::error('AdminAuthController::updateAdminProfile error: ' . $e->getMessage());
            return redirect()->back()->with("error", 'An error occurred');
        }
    }

    public function adminDashboard()
    {
        return view("admin.dashboard.index");
    }


}
