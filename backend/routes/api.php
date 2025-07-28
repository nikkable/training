<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Wilberries\WilberriesParser;
use App\Http\Controllers\KafkaTestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/send-kafka-message', [KafkaTestController::class, 'sendMessage']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/parse', function (Request $request) {
    $validated = $request->validate([
        'url' => 'required|url',
    ]);

    $parser = app(WilberriesParser::class);
    try {
        $result = $parser->parse($validated['url']);
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Тестовый
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello from Laravel API!']);
});

// Регистрация
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully!',
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 201);
});

// Авторизация
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Logged in successfully!',
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

// Профиль
Route::middleware('auth:sanctum')->get('/user-profile', function (Request $request) {
    return response()->json([
        'message' => 'Welcome to your profile!',
        'user' => $request->user(),
        'api_status' => 'Authorized'
    ]);
});

// Выход
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->tokens()->delete(); // Удаляем все токены пользователя
    return response()->json(['message' => 'Logged out successfully!']);
});
