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
- `admin`: gestiona usuaris i instruments, i pot veure reserves.

- `user`: pot veure instruments i gestionar les seves reserves.



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
