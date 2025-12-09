<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function verify(Request $request)
    {
        $password = $request->input('password');

        if ($password === "superadmin123") {
            session(['admin_verified' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 401);
    }
}
