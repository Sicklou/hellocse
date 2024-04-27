<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        /** @var \App\Models\User $user */
        $user = User::where('email', $request->email)->first();

        if ($user && $user->isNotAdmin()) {
            throw ValidationException::withMessages([
                'email' => ['Authentification impossible'],
            ])
            ->status(403);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ])
            ->status(401);
        }

        return response()->json(['token' => $user->createToken("default")->plainTextToken], 200);
    }
}
