<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Instruments",
    description: "Gestió d'instruments"
)]
class Instruments
{
    #[OA\Get(
        path: "/api/instruments",
        tags: ["Instruments"],
        summary: "List all instruments",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/InstrumentListResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/instruments",
        tags: ["Instruments"],
        summary: "Create new instrument (admin only)",
        security: [["passport" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "description", "type", "status"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Guitar 1"),
                    new OA\Property(property: "description", type: "string", example: "Electric guitar"),
                    new OA\Property(property: "type", type: "string", example: "STRING"),
                    new OA\Property(property: "status", type: "string", example: "AVAILABLE"),
                    new OA\Property(property: "image_path", type: "string", nullable: true, example: null),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Instrument created successfully", content: new OA\JsonContent(ref: "#/components/schemas/Instrument")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
            new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: "/api/instruments/{id}",
        tags: ["Instruments"],
        summary: "Get instrument by ID",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/Instrument")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 404, description: "Instrument not found"),
        ]
    )]
    public function show() {}

    #[OA\Put(
        path: "/api/instruments/{id}",
        tags: ["Instruments"],
        summary: "Update instrument (admin only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Updated Guitar 1"),
                    new OA\Property(property: "description", type: "string", example: "Updated description"),
                    new OA\Property(property: "type", type: "string", example: "STRING"),
                    new OA\Property(property: "status", type: "string", example: "AVAILABLE"),
                    new OA\Property(property: "image_path", type: "string", nullable: true, example: null),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Instrument updated successfully", content: new OA\JsonContent(ref: "#/components/schemas/Instrument")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
            new OA\Response(response: 404, description: "Instrument not found"),
            new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/instruments/{id}",
        tags: ["Instruments"],
        summary: "Delete instrument (admin only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: "Instrument deleted successfully", content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
            new OA\Response(response: 404, description: "Instrument not found"),
        ]
    )]
    public function destroy() {}
}
