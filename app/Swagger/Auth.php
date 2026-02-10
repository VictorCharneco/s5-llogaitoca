<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Auth",
    description: "Autenticació (register/login/logout/me)"
)]
class Auth
{
    #[OA\Post(
        path: "/api/register",
        tags: ["Auth"],
        summary: "Register a new user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "User Test"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "test@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
        ]
    )]
    public function register() {}

    #[OA\Post(
        path: "/api/login",
        tags: ["Auth"],
        summary: "Login and get access token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "test@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(response: 422, description: "Invalid credentials"),
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: "/api/logout",
        tags: ["Auth"],
        summary: "Logout and revoke token",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully", content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function logout() {}

    #[OA\Get(
        path: "/api/me",
        tags: ["Auth"],
        summary: "Get authenticated user info",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/User")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function me() {}
}