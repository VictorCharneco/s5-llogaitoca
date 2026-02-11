<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Meetings",
    description: "Gestió de quedades"
)]


class Meetings{
    #[OA\Get(
        path: "/api/meetings",
        tags: ["Meetings"],
        summary: "List all meetings (admin only)",
        security: [["passport" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/MeetingListResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - Admin only"
            ),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: "/api/meetings",
        tags: ["Meetings"],
        summary: "Create a meeting (user only, needs active reservation)",
        security: [["passport" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["reservation_id", "room", "day", "start_time", "end_time"],
                properties: [
                    new OA\Property(property: "reservation_id", type: "integer", example: 1),
                    new OA\Property(property: "room", type: "string", enum: ["SPRINGSTEEN","DYLAN","ARMSTRONG","MARTIN"], example: "DYLAN"),
                    new OA\Property(property: "day", type: "string", format: "date", example: "2026-02-12"),
                    new OA\Property(property: "start_time", type: "string", example: "18:00"),
                    new OA\Property(property: "end_time", type: "string", example: "19:00"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Meeting created",
                content: new OA\JsonContent(ref: "#/components/schemas/MeetingCreateResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User only"
            ),
            new OA\Response(
                response: 422,
                description: "Business rules: needs ACTIVE reservation, day must be within reservation dates, no room overlap, no user overlap.",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            ),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: "/api/meetings/my",
        tags: ["Meetings"],
        summary: "Get my meetings (user only)",
        security: [["passport" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/MeetingListResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
        ]
    )]
    public function myMeetings(): void {}

    #[OA\Post(
        path: "/api/meetings/{id}/join",
        tags: ["Meetings"],
        summary: "Join a meeting (user only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Joined",
                content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Meeting not found"
            ),
            new OA\Response(
                response: 422,
                description: "Business rules: already joined, meeting full (max 4), needs ACTIVE reservation, no user overlap.",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            ),
        ]
    )]
    public function join(): void {}

    #[OA\Post(
        path: "/api/meetings/{id}/quit",
        tags: ["Meetings"],
        summary: "Quit a meeting (user only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Quit",
                content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Meeting not found"
            ),
            new OA\Response(
                response: 422,
                description: "Business rules: you are not in this meeting",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            ),
        ]
    )]
    public function quit(): void {}

    #[OA\Delete(
        path: "/api/meetings/{id}",
        tags: ["Meetings"],
        summary: "Delete a meeting (admin only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Deleted",
                content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - Admin only"
            ),
            new OA\Response(
                response: 404,
                description: "Meeting not found"
            ),
        ]
    )]
    public function destroy(): void {}

    #[OA\Patch(
        path: "/api/meetings/{id}/status",
        tags: ["Meetings"],
        summary: "Update meeting status (admin only)",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", enum: ["ACTIVE","FINISHED","CANCELLED"], example: "CANCELLED"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Updated",
                content: new OA\JsonContent(ref: "#/components/schemas/MeetingUpdateStatusResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - Admin only"
            ),
            new OA\Response(
                response: 404,
                description: "Meeting not found"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            ),
        ]
    )]
    public function updateStatus(): void {}
}