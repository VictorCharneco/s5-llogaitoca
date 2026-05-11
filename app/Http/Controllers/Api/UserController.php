<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\DestroyUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse{
        $users = User::query()
                ->select(['id', 'name', 'email', 'role', 'created_at', 'updated_at'])
                ->orderBy('id', 'asc')
                ->paginate(15);
        return response()->json($users);
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

    public function destroy(DestroyUserRequest $request, int $id):JsonResponse{
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuari no trobat',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuari eliminat correctament',
        ], 200);
    }

}
