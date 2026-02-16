<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse{
        $users = User::query()
                ->select(['id', 'name', 'email', 'role', 'created_at', 'updated_at'])
                ->orderBy('id', 'asc')
                ->get();
        return response()->json(['data' => $users,]);
    }

    public function show (int $id): JsonResponse{
        $user = User::query()
                ->select(['id', 'name', 'email', 'role', 'created_at', 'updated_at'])
                ->find($id);

        if(!$user){
            return response()->json(['message' => 'Usuari no trobat',], 404);
        }

        return response()->json(['data' => $user,]);
    }

    public function destroy(int $id): JsonResponse{
        $authUser = request()->user();

        if (!$authUser) {
            return response()->json(['message' => '⚠️ No autenticat'], 401);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuari no trobat'], 404);
        }

        $isAdmin = ($authUser->role ?? null) === 'admin';
        $isSelf  = $authUser->id === $user->id;

        if (!$isAdmin && !$isSelf) {
            return response()->json(['message' => '⛔️ Prohibit'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuari eliminat'], 200);
    }

}
