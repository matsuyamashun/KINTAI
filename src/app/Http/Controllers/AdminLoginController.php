<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        //ログイン失敗
        if (!Auth::guard('admin')->attempt($credentials)) {
            return back()->withErros([
                'email' => 'ログイン情報が登録されてません',
            ]);
        }

        $user = Auth::guard('admin')->user();

        if (!$user->role === 1) {
            Auth::guard('admin')->logout();
            return back();
        }

        //ここでログイン
        return redirect()->route('admin.attendance_list');
    }
}