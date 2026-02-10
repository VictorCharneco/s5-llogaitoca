<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    required: ["id", "name", "email", "role"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Test User"),
        new OA\Property(property: "email", type: "string", format: "email", example: "test@example.com"),
        new OA\Property(property: "role", type: "string", enum: ["admin", "user"], example: "user"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
    ]
)]
#[OA\Schema(
    schema: "Instrument",
    required: ["id", "name", "description", "type", "status"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Guitar 1"),
        new OA\Property(property: "description", type: "string", example: "Electric guitar"),
        new OA\Property(property: "type", type: "string", enum: ["STRING", "WIND", "PERCUSSION", "KEYBOARD"], example: "STRING"),
        new OA\Property(property: "status", type: "string", enum: ["AVAILABLE", "OUT_OF_STOCK", "MAINTENANCE"], example: "AVAILABLE"),
        new OA\Property(property: "image_path", type: "string", nullable: true, example: "instruments/guitar.jpg"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
    ]
)]
#[OA\Schema(
    schema: "Reservation",
    required: ["id", "user_id", "instrument_id", "start_date", "end_date", "status"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 3),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "instrument_id", type: "integer", example: 1),
        new OA\Property(property: "start_date", type: "string", format: "date", example: "2026-02-10"),
        new OA\Property(property: "end_date", type: "string", format: "date", example: "2026-02-12"),
        new OA\Property(property: "status", type: "string", enum: ["ACTIVE", "FINISHED"], example: "ACTIVE"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
    ]
)]
#[OA\Schema(
    schema: "ReservationWithInstrument",
    required: ["id", "user_id", "instrument_id", "start_date", "end_date", "status", "instrument"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 3),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "instrument_id", type: "integer", example: 1),
        new OA\Property(property: "start_date", type: "string", format: "date", example: "2026-02-10"),
        new OA\Property(property: "end_date", type: "string", format: "date", example: "2026-02-12"),
        new OA\Property(property: "status", type: "string", enum: ["ACTIVE", "FINISHED"], example: "ACTIVE"),
        new OA\Property(property: "instrument", ref: "#/components/schemas/Instrument"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-02-09T10:30:00.000000Z"),
    ]
)]
#[OA\Schema(
    schema: "AuthResponse",
    required: ["token", "user"],
    properties: [
        new OA\Property(property: "token", type: "string", example: "1|qwertyuiopasdfghjklzxcvbnm1234567890"),
        new OA\Property(property: "user", ref: "#/components/schemas/User"),
    ]
)]
#[OA\Schema(
    schema: "MessageResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Action completed successfully."),
    ]
)]
#[OA\Schema(
    schema: "ValidationError",
    properties: [
        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
        new OA\Property(
            property: "errors",
            type: "object",
            additionalProperties: new OA\AdditionalProperties(
                type: "array",
                items: new OA\Items(type: "string")
            )
        ),
    ]
)]
#[OA\Schema(
    schema: "ErrorResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
    ]
)]
#[OA\Schema(
    schema: "UserListResponse",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/User")
        )
    ]
)]
#[OA\Schema(
    schema: "InstrumentListResponse",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Instrument")
        )
    ]
)]
#[OA\Schema(
    schema: "ReservationListResponse",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/ReservationWithInstrument")
        )
    ]
)]
class Schemas {}