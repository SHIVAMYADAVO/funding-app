<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\user;
use Hash;

class AdminController extends Controller
{
    public function dashboard(){
        $user = User::where('role', '1')->count();
        return view('dashboard',compact('user'));
    }
    public function authenticate(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($validator->fails()) {
        return redirect()->route('admin.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
    }

    // Attempt login
    if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
        
        $admin = Auth::guard('admin')->user();

        // Check if user is authorized
        if ($admin->role == '0') {
            return redirect()->route('admin.dashboard');
        } else {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', 'You are not authorized to access admin panel.');
        }
    }

    // Failed login response
    return redirect()->route('admin.login')
        ->with('error', 'Either Email or Password is incorrect.');
}
public function productindex(){
    return view('product.index');
}
public function userindex(){
    $products = User::where('role', '!=', 0)->get(); 
   return view('usermangement.index',compact('products'));
}
public function userdelete($id){
   $products = user::find($id);
   $products->delete();
    return redirect()->route('usermangement.index')->with('success', 'User deleted successfully.');

 }
 public function logout(Request $request) {
    Auth::guard('admin')->logout(); // Logout admin user
    $request->session()->invalidate(); // Invalidate the session
    $request->session()->regenerateToken(); // Regenerate CSRF token

    return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
}
public function usercreate(){
    
   return view('usermangement.create');
}
public function addUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:550',
        'phone' => 'required|string|unique:users,phone|max:20',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:6|confirmed',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/profile'), $imageName);
        $userimage = 'uploads/profile/' . $imageName;
    }
    $user = User::create([
        'name' => $request->name,
        'address' => $request->address,
        'phone' => $request->phone,
        'email' => $request->email,
        'role' => $request->role,
        'image' => $userimage,
        'password' => Hash::make($request->password)
    ]);

    return redirect()->back()->with('success', 'User Registration successfully.');

}
}
