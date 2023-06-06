<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Http\Responses\TokenResponse;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
