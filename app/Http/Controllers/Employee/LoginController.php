<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * The employee's login page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        // if there is an authenticated user, redirect to employee index
        if (auth()->check()) {
            return redirect('/employee/index');
        }

        return view('employee/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'barcode' => ['required', 'string'],
            'pin_code' => ['required', 'string'],
        ]);

        $user = User::where('barcode', $credentials['barcode'])->first();

        if (!$user || !$user->employee) {
            return response()->json('User or Employee not found', 404);
        }

        if (Auth::attempt(['barcode' => $credentials['barcode'], 'password' => $credentials['pin_code']])) {
            $request->session()->regenerate();

            return true;
        }

        return response()->json('User or Employee not found', 404);
    }
}
