<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "Llogaitoca API",
        version: "1.0.0",
        description: <<<DESC
API REST per a lloguer d'instruments (Sprint 5).

**Autenticació**
- Usa Laravel Passport amb token Bearer.

- En endpoints protegits: header `Authorization: Bearer {token}`.


**Rols**
- `admin`: gestiona usuaris i instruments, pot veure les reserves i gestionar les trobades (meetings).

- `user`: pot veure instruments, gestionar les seves reserves i crear/gestionar la seva participació en meetings.


**Meetings**
- Per crear un meeting cal una **reserva ACTIVE** del mateix user.

- El `day` del meeting ha d'estar **dins** el rang `start_date` / `end_date` de la reserva.

- **Rooms disponibles**: `SPRINGSTEEN`, `DYLAN`, `ARMSTRONG`, `MARTIN`.

- **Màxim 4 persones** per meeting.

- No es permet **solapar horaris**:
  - mateixa sala (room overlap)
  - mateix usuari (user overlap)

- Admin pot marcar `status`: `ACTIVE`, `FINISHED`, `CANCELLED`.


**Com provar ràpid a Swagger UI**

1) `POST /api/register` o `POST /api/login`

2) Copia el token

3) A Swagger: botó **Authorize** → enganxa **NOMÉS** el token (sense escriure "Bearer ")

**A Postman / Thunder Client**

- Header: `Authorization: Bearer {token}`
DESC
    ),
    servers: [
        new OA\Server(
            url: "http://127.0.0.1:8000",
            description: "Local"
        )
    ]
)]
#[OA\SecurityScheme(
    securityScheme: "passport",
    type: "http",
    scheme: "bearer",
    description: "Bearer token (Laravel Passport). A Swagger UI enganxa NOMÉS el token (sense 'Bearer ')."
)]
class OpenApi {}
