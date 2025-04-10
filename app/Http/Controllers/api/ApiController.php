<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Favorite;
class ApiController extends Controller
{

public function registerUser(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:550',
        'phone' => 'required|string|unique:users,phone|max:20',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'address' => $request->address,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

  
    return response()->json(['status' => 'success', 'message' => 'Registration successful!' ,'data' => [
        'user' => $user,
    ],], 201);
}

public function loginUser(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string|min:6',
    ]);

    // $credentials = $request->only('email', 'password');
    $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    
    $credentials = [$loginField => $request->email, 'password' => $request->password];

    if (!($token = JWTAuth::attempt($credentials))) {
        return response()->json(
            [
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ],
            400,
        );
    }
    return $this->responeswithtoken($token);
}
protected function responeswithtoken($token)
{
    return response()->json(
        [
            'success' => true,
            'message' => 'User logged in successfully.',
            'data' => [
                'token_type' => 'bearer',
                'access_token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 464465353454316000,
            ],
        ],
        200,
    );
}
public function profile()
{
    $user = auth()->user();

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'address' => $user->address,
        'phone' => $user->phone,
        'email' => $user->email,
        'image' => $user->image ? asset($user->image) : null, // Add asset() for image
    ]);
}
public function refresh()
{
    return $this->responeswithtoken(auth()->refresh());

}

public function logout(Request $request)
{
    auth()->logout();
    return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
}
public function forgotpasswordsendOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::where('phone', $request->phone)
                ->where('email', $request->email)
                ->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found!'
        ], 404);
    }

    $user->update([
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Password updated successfully!'
    ], 200);
}

   public function bannerget() {
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }
    $banners = Banner::all()->map(function ($banner) {
        return [
            'id' => $banner->id,
            'image' => asset('uploads/banners/' .$banner->image),
            'created_at' => $banner->created_at,
            'updated_at' => $banner->updated_at,
        ];
    });

    return response()->json([
        'status' => 'success',
        'message' => 'banner retrieved successfully',
        'data' => [
                    'banner' => $banners,
                ],
      
    ]);
}
public function categoryget() {
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }
    $categories = Category::all()->map(function ($category) {
        return [
            'id' => $category->id,
            'image' => asset($category->image), // Corrected path
            'name' => $category->name,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Categories retrieved successfully',
        'data' => [
            'categories' => $categories,
        ],
    ]);
}
public function productget(Request $request) {
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }

    // Query builder with filters
    $query = Product::query();

    // Filter by category_id if provided
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by product name (case-insensitive search)
    if ($request->has('name')) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }

    // Retrieve filtered products
    $products = $query->get()->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => asset($product->image),
            'category' => [
                    'id' => $product->category->id ?? null,
                    'name' => $product->category->name ?? 'Unknown',
                ],
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Products retrieved successfully',
        'data' => [
            'products' => $products,
        ],
    ]);
}

public function updateProfile(Request $request) {
    $user = auth()->user(); // Get the authenticated user
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }
    // Validation
    $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:15',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Handle Image Upload
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/profile'), $imageName);
        $user->image = 'uploads/profile/' . $imageName;
    }

    // Update User Data
    $user->update([
        'name' => $request->name,
        'address' => $request->address,
        'phone' => $request->phone,
        'email' => $request->email,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'address' => $user->address,
            'phone' => $user->phone,
            'email' => $user->email,
            'image' => asset($user->image), // Correct image path
        ]
    ]);
}
public function toggleFavorite(Request $request) {
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }

    $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $user = auth()->user();
    $product_id = $request->product_id;

    // Check if product is already in favorites
    $favorite = Favorite::where('user_id', $user->id)->where('product_id', $product_id)->first();

    if ($favorite) {
        // Remove from favorites
        $favorite->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from favorites'
        ]);
    } else {
        // Add to favorites
        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Product added to favorites'
        ]);
    }
}
public function getFavorites() {
    if (!auth()->check()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Token is missing or invalid'
        ], 401);
    }

    $favorites = Favorite::where('user_id', auth()->id())->pluck('product_id');

    $products = Product::whereIn('id', $favorites)->get()->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => asset($product->image),
            'category' => [
                    'id' => $product->category->id ?? null,
                    'name' => $product->category->name ?? 'Unknown',
                ],
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Favorite products retrieved successfully',
        'data' => [
            'products' => $products,
        ],
    ]);
}

}
