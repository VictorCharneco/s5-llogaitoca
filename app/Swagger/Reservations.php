<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Reservations",
    description: "Gestió de reserves"
)]
class Reservations
{
    #[OA\Post(
        path: "/api/instruments/{id}/reserve",
        tags: ["Reservations"],
        summary: "Reserve an instrument",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["start_date", "end_date"],
                properties: [
                    new OA\Property(property: "start_date", type: "string", format: "date", example: "2026-02-10"),
                    new OA\Property(property: "end_date", type: "string", format: "date", example: "2026-02-12"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Reservation created successfully", content: new OA\JsonContent(ref: "#/components/schemas/Reservation")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 404, description: "Instrument not found"),
            new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
        ]
    )]
    public function reserve() {}

    #[OA\Get(
        path: "/api/reservations/my",
        tags: ["Reservations"],
        summary: "Get my reservations",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/ReservationListResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
        ]
    )]
    public function myReservations() {}

    #[OA\Get(
        path: "/api/reservations",
        tags: ["Reservations"],
        summary: "List all reservations (admin only)",
        security: [["passport" => []]],
        responses: [
            new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(ref: "#/components/schemas/ReservationListResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden - Admin only"),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/reservations/{id}/return",
        tags: ["Reservations"],
        summary: "Return a reservation",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: "Returned successfully", content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Reservation not found"),
        ]
    )]
    public function returnReservation() {}

    #[OA\Delete(
        path: "/api/reservations/{id}",
        tags: ["Reservations"],
        summary: "Delete a reservation",
        description: "User pot eliminar la seva reserva; admin pot eliminar qualsevol (segons el teu codi).",
        security: [["passport" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted successfully", content: new OA\JsonContent(ref: "#/components/schemas/MessageResponse")),
            new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Reservation not found"),
        ]
    )]
    public function destroy() {}
}
