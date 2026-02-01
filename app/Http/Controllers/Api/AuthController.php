<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function register(): JsonResponse{
        return response()->json(['message' => 'todo: register'], 501);
    }

    public function login(): JsonResponse{
        return response()->json(['message' => 'todo: login'], 501);
    }

    public function logout(): JsonResponse{
        return response()->json(['message' => 'todo: logout'], 501);
    }

    public function me(): JsonResponse{
        return response()->json(['message' => 'todo: me'], 501);
    }



}