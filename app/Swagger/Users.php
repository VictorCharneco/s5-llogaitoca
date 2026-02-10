<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Users",
    description: "Gestió d'usuaris (admin)"
)]
class Users
{
    #[OA\Get(
        path: "/api/users",
        tags: ["Users"],
        summary: "List all users (admin only)",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/UserListResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: "/api/users/{id}",
        tags: ["Users"],
        summary: "Get user by ID (admin only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/User")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
            new OA\Response(response: 404, description: "User not found"),
        ]
    )]
    public function show() {}
}
