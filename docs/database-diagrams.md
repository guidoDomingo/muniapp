# MuniApp - Diagramas de Base de Datos y Arquitectura
## Sistema de Gestión Municipal - Tesis Universitaria

Este documento contiene diagramas profesionales del sistema MuniApp desarrollado en Laravel, incluyendo diagramas de entidad-relación, diagramas de secuencia, y arquitectura del sistema.

---

## 1. Diagrama de Entidad-Relación (ERD) - Base de Datos Principal

```mermaid
---
title: "MuniApp - Modelo de Base de Datos"
---
erDiagram
    %% Tabla de Usuarios
    USERS {
        bigint id PK "Identificador único del usuario"
        string name "Nombre completo del usuario"
        string email UK "Correo electrónico único"
        timestamp email_verified_at "Fecha de verificación de email"
        string password "Contraseña encriptada"
        string remember_token "Token de recordar sesión"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Trámites
    TRAMITES {
        bigint id PK "Identificador único del trámite"
        string nombre "Nombre descriptivo del trámite"
        text descripcion "Descripción detallada del trámite"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Solicitudes
    SOLICITUDES {
        bigint id PK "Identificador único de la solicitud"
        bigint user_id FK "Referencia al usuario solicitante"
        bigint tramite_id FK "Referencia al trámite solicitado"
        json formulario "Datos del formulario en formato JSON"
        string detalles "Detalles adicionales de la solicitud"
        text comentario "Comentarios del usuario"
        decimal latitud "Coordenada de latitud para ubicación"
        decimal longitud "Coordenada de longitud para ubicación"
        string estado "Estado actual de la solicitud"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de FAQs
    FAQS {
        bigint id PK "Identificador único de la pregunta"
        string pregunta "Pregunta frecuente"
        text respuesta "Respuesta a la pregunta"
        boolean activo "Indica si está activa o no"
        integer orden "Orden de visualización"
        string categoria "Categoría de la pregunta"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Chat
    CHATS {
        bigint id PK "Identificador único del mensaje"
        bigint user_id FK "Referencia al usuario que envía"
        string message "Contenido del mensaje"
        string room "Sala de chat (por defecto: general)"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Roles (Sistema de Permisos)
    ROLES {
        bigint id PK "Identificador único del rol"
        string name "Nombre del rol"
        string guard_name "Nombre del guardian"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Permisos
    PERMISSIONS {
        bigint id PK "Identificador único del permiso"
        string name "Nombre del permiso"
        string guard_name "Nombre del guardian"
        timestamp created_at "Fecha de creación"
        timestamp updated_at "Fecha de actualización"
    }

    %% Tabla de Relación Usuario-Rol
    MODEL_HAS_ROLES {
        bigint role_id PK,FK "Referencia al rol"
        string model_type PK "Tipo de modelo (User)"
        bigint model_id PK,FK "ID del modelo (user_id)"
    }

    %% Tabla de Relación Rol-Permiso
    ROLE_HAS_PERMISSIONS {
        bigint permission_id PK,FK "Referencia al permiso"
        bigint role_id PK,FK "Referencia al rol"
    }

    %% Relaciones principales
    USERS ||--o{ SOLICITUDES : "realiza"
    TRAMITES ||--o{ SOLICITUDES : "genera"
    USERS ||--o{ CHATS : "envía"
    
    %% Relaciones del sistema de permisos
    ROLES ||--o{ MODEL_HAS_ROLES : "asignado_a"
    USERS ||--o{ MODEL_HAS_ROLES : "tiene"
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : "incluye"
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : "contiene"

    %% Estilos para mayor claridad
    classDef primaryEntity fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef businessEntity fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef systemEntity fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef relationEntity fill:#fff3e0,stroke:#e65100,stroke-width:2px

    class USERS,TRAMITES,SOLICITUDES primaryEntity
    class FAQS,CHATS businessEntity
    class ROLES,PERMISSIONS systemEntity
    class MODEL_HAS_ROLES,ROLE_HAS_PERMISSIONS relationEntity
```

---

## 2. Diagrama de Secuencia - Proceso de Solicitud de Trámite

```mermaid
sequenceDiagram
    participant C as Ciudadano
    participant W as Aplicación Web
    participant A as Autenticación
    participant DB as Base de Datos
    participant N as Sistema de Notificaciones
    participant Admin as Administrador

    Note over C,Admin: Flujo completo de solicitud de trámite municipal

    %% Inicio de sesión
    C->>+W: Accede a la aplicación
    W->>+A: Verificar autenticación
    alt Usuario no autenticado
        A-->>W: Usuario no autenticado
        W-->>C: Redirigir a login
        C->>W: Completar formulario de login
        W->>A: Validar credenciales
        A->>+DB: Verificar usuario/contraseña
        DB-->>-A: Credenciales válidas
        A-->>-W: Autenticación exitosa
    else Usuario autenticado
        A-->>-W: Usuario autenticado
    end

    %% Selección de trámite
    W-->>-C: Mostrar dashboard principal
    C->>+W: Navegar a sección de trámites
    W->>+DB: Obtener lista de trámites disponibles
    DB-->>-W: Lista de trámites
    W-->>-C: Mostrar trámites disponibles

    C->>+W: Seleccionar trámite específico
    W->>+DB: Obtener detalles del trámite
    DB-->>-W: Detalles y formulario del trámite
    W-->>-C: Mostrar formulario de solicitud

    %% Completar solicitud
    rect rgb(200, 255, 200)
    Note right of C: Proceso de completar solicitud
    C->>+W: Completar formulario de solicitud
    C->>W: Agregar ubicación (GPS)
    C->>W: Agregar comentarios adicionales
    C->>W: Enviar solicitud
    end

    %% Validación y procesamiento
    W->>+W: Validar datos del formulario
    alt Datos válidos
        W->>+DB: Crear nueva solicitud
        activate DB
        DB->>DB: Insertar en tabla solicitudes
        DB->>DB: Relacionar con usuario y trámite
        DB-->>W: Solicitud creada (ID generado)
        deactivate DB
        
        %% Notificación al usuario
        W->>+N: Generar notificación de confirmación
        N-->>-C: "Solicitud enviada correctamente"
        
        %% Notificación al administrador
        W->>+N: Notificar nueva solicitud a admin
        N-->>Admin: "Nueva solicitud pendiente de revisión"
        
        W-->>-C: Mostrar mensaje de éxito
    else Datos inválidos
        W-->>C: Mostrar errores de validación
        C->>W: Corregir datos
    end

    %% Seguimiento de la solicitud
    Note over C,Admin: El ciudadano puede hacer seguimiento

    C->>+W: Ver estado de solicitudes
    W->>+DB: Consultar solicitudes del usuario
    DB-->>-W: Lista de solicitudes con estado
    W-->>-C: Mostrar historial de solicitudes

    %% Procesamiento por parte del administrador
    rect rgb(255, 200, 200)
    Note right of Admin: Proceso administrativo
    Admin->>+W: Revisar solicitudes pendientes
    W->>+DB: Obtener solicitudes pendientes
    DB-->>-W: Lista de solicitudes
    W-->>Admin: Mostrar panel de administración

    Admin->>+W: Actualizar estado de solicitud
    W->>+DB: Actualizar estado en base de datos
    DB-->>-W: Estado actualizado
    
    W->>+N: Notificar cambio de estado al ciudadano
    N-->>-C: "Su solicitud ha sido actualizada"
    W-->>-Admin: Confirmación de actualización
    end
```

---

## 3. Diagrama de Secuencia - Sistema de Chat en Tiempo Real

```mermaid
sequenceDiagram
    participant U1 as Usuario 1
    participant U2 as Usuario 2
    participant W as Frontend
    participant WS as WebSocket Server
    participant API as API Laravel
    participant DB as Base de Datos
    participant Echo as Laravel Echo

    Note over U1,Echo: Sistema de chat en tiempo real con WebSockets

    %% Conexión inicial
    rect rgb(230, 255, 230)
    U1->>+W: Acceder al chat
    W->>+Echo: Inicializar conexión WebSocket
    Echo->>+WS: Establecer conexión
    WS-->>-Echo: Conexión establecida
    Echo-->>-W: Conectado al canal 'chat.general'
    W-->>-U1: Chat listo para usar
    end

    %% Usuario 2 se conecta
    U2->>+W: Acceder al chat
    W->>+Echo: Inicializar conexión WebSocket
    Echo->>+WS: Establecer conexión
    WS-->>-Echo: Conexión establecida
    Echo-->>-W: Conectado al canal 'chat.general'
    W-->>-U2: Chat listo para usar

    %% Envío de mensaje
    rect rgb(255, 230, 230)
    Note right of U1: Usuario 1 envía mensaje
    U1->>+W: Escribir y enviar mensaje
    W->>W: Validar mensaje localmente
    
    %% Envío via API REST
    W->>+API: POST /chat (mensaje, room)
    API->>API: Validar autenticación
    API->>API: Validar datos del mensaje
    
    API->>+DB: Guardar mensaje en tabla chats
    activate DB
    DB->>DB: INSERT INTO chats
    DB-->>API: Mensaje guardado (ID generado)
    deactivate DB
    
    %% Broadcasting del evento
    API->>+Echo: Broadcast NewChatMessage event
    Echo->>+WS: Enviar evento a canal 'chat.general'
    WS-->>Echo: Evento enviado
    Echo-->>-API: Broadcasting completado
    
    API-->>-W: Respuesta: mensaje enviado exitosamente
    W-->>-U1: Mostrar mensaje enviado
    end

    %% Recepción en tiempo real
    rect rgb(230, 230, 255)
    Note right of U2: Usuario 2 recibe mensaje en tiempo real
    WS->>+Echo: Evento NewChatMessage
    Echo->>+W: Mensaje recibido del canal
    W->>W: Validar que no es mensaje propio
    W->>W: Agregar mensaje a la interfaz
    W->>W: Reproducir sonido de notificación
    W-->>-U2: Mostrar nuevo mensaje
    deactivate Echo
    end

    %% Sistema de polling como fallback
    rect rgb(255, 255, 230)
    Note over W,DB: Sistema de fallback si WebSocket falla
    loop Cada 3 segundos
        W->>+API: GET /chat/messages (polling)
        API->>+DB: Consultar mensajes nuevos
        DB-->>-API: Lista de mensajes recientes
        API-->>-W: Nuevos mensajes (si los hay)
        alt Hay mensajes nuevos
            W->>W: Mostrar mensajes no recibidos por WS
            W-->>U2: Actualizar interfaz
        end
    end
    end

    %% Desconexión
    Note over U1,Echo: Gestión de desconexiones

    U1->>+W: Salir del chat
    W->>+Echo: Desconectar WebSocket
    Echo->>+WS: Cerrar conexión
    WS-->>-Echo: Conexión cerrada
    Echo-->>-W: Desconectado
    W-->>-U1: Salir de la aplicación
```

---

## 4. Diagrama de Arquitectura del Sistema

```mermaid
graph TB
    %% Frontend Layer
    subgraph "Frontend - Navegador Web"
        Blade[Blade Templates]
        JS[JavaScript/Vite]
        CSS[CSS/Bootstrap]
        Echo[Laravel Echo Client]
    end

    %% Backend Layer
    subgraph "Backend - Laravel Application"
        WebRoutes[Rutas Web]
        APIRoutes[Rutas API]
        Controllers[Controladores]
        Models[Modelos Eloquent]
        Middleware[Middleware]
        Events[Eventos y Listeners]
        WebSocket[WebSocket Server]
    end

    %% Database Layer
    subgraph "Capa de Datos"
        MySQL[(MySQL Database)]
        Migrations[Migraciones]
        Seeders[Seeders]
    end

    %% External Services
    subgraph "Servicios Externos"
        Pusher[Pusher/WebSockets]
        Maps[Google Maps API]
        Email[Servicio de Email]
    end

    %% Connections Frontend to Backend
    Blade --> WebRoutes
    JS --> APIRoutes
    Echo --> WebSocket

    %% Backend Internal Connections
    WebRoutes --> Controllers
    APIRoutes --> Controllers
    Controllers --> Models
    Controllers --> Middleware
    Controllers --> Events
    Events --> WebSocket

    %% Backend to Database
    Models --> MySQL
    Migrations --> MySQL
    Seeders --> MySQL

    %% Backend to External Services
    WebSocket --> Pusher
    Controllers --> Maps
    Events --> Email

    %% Styles
    classDef frontend fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef backend fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef database fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef external fill:#fff3e0,stroke:#e65100,stroke-width:2px

    class Blade,JS,CSS,Echo frontend
    class WebRoutes,APIRoutes,Controllers,Models,Middleware,Events,WebSocket backend
    class MySQL,Migrations,Seeders database
    class Pusher,Maps,Email external
```

---

## 5. Diagrama de Flujo - Autenticación y Autorización

```mermaid
sequenceDiagram
    participant User as Usuario
    participant Guard as Laravel Guard
    participant Middleware as Auth Middleware
    participant Spatie as Spatie Permissions
    participant DB as Base de Datos

    Note over User,DB: Sistema de autenticación y autorización con roles

    %% Login Process
    rect rgb(240, 248, 255)
    User->>+Guard: Intentar login (email, password)
    Guard->>+DB: Verificar credenciales en tabla users
    DB-->>-Guard: Usuario encontrado/no encontrado
    
    alt Credenciales válidas
        Guard->>+DB: Obtener roles del usuario
        DB->>+DB: JOIN users, model_has_roles, roles
        DB-->>-Guard: Lista de roles asignados
        Guard->>Guard: Crear sesión de usuario
        Guard-->>User: Login exitoso + roles
    else Credenciales inválidas
        Guard-->>User: Error de autenticación
    end
    end

    %% Authorization Check
    rect rgb(248, 255, 240)
    Note right of User: Usuario intenta acceder a recurso protegido
    User->>+Middleware: Solicitar acceso a recurso
    Middleware->>+Guard: Verificar autenticación
    Guard-->>Middleware: Usuario autenticado
    
    Middleware->>+Spatie: Verificar permisos/roles necesarios
    Spatie->>+DB: Consultar permisos del usuario
    DB->>+DB: JOIN con tablas de roles y permisos
    DB-->>-Spatie: Permisos del usuario
    
    alt Usuario tiene permisos
        Spatie-->>Middleware: Acceso autorizado
        Middleware-->>User: Acceso concedido al recurso
    else Usuario sin permisos
        Spatie-->>Middleware: Acceso denegado
        Middleware-->>User: Error 403 - Acceso denegado
    end
    end

    %% Role Assignment (Admin function)
    rect rgb(255, 248, 240)
    Note right of User: Administrador asigna rol a usuario
    User->>+Spatie: Asignar rol a usuario (admin function)
    Spatie->>+DB: Verificar si rol existe
    DB-->>Spatie: Rol encontrado
    
    Spatie->>+DB: INSERT en model_has_roles
    DB-->>-Spatie: Rol asignado exitosamente
    Spatie-->>-User: Confirmación de asignación
    end
```

---

## 6. Diagrama de Casos de Uso del Sistema

```mermaid
sequenceDiagram
    participant Ciudadano
    participant Sistema as MuniApp Sistema
    participant Admin as Administrador
    participant BD as Base de Datos

    Note over Ciudadano,BD: Casos de uso principales del sistema MuniApp

    %% Caso de Uso 1: Registro de Usuario
    rect rgb(230, 255, 255)
    Ciudadano->>+Sistema: Registrarse en el sistema
    Sistema->>Sistema: Validar datos de registro
    Sistema->>+BD: Crear nuevo usuario
    BD-->>-Sistema: Usuario creado exitosamente
    Sistema-->>-Ciudadano: Confirmación de registro
    end

    %% Caso de Uso 2: Consultar Trámites Disponibles
    rect rgb(255, 230, 255)
    Ciudadano->>+Sistema: Consultar trámites disponibles
    Sistema->>+BD: Obtener lista de trámites
    BD-->>-Sistema: Lista de trámites activos
    Sistema-->>-Ciudadano: Mostrar trámites disponibles
    end

    %% Caso de Uso 3: Realizar Solicitud
    rect rgb(255, 255, 230)
    Ciudadano->>+Sistema: Iniciar nueva solicitud
    Sistema->>Sistema: Mostrar formulario de trámite
    Ciudadano->>Sistema: Completar formulario
    Sistema->>Sistema: Validar información
    Sistema->>+BD: Guardar solicitud
    BD-->>-Sistema: Solicitud guardada
    Sistema-->>-Ciudadano: Confirmación de solicitud
    end

    %% Caso de Uso 4: Seguimiento de Solicitudes
    rect rgb(230, 255, 230)
    Ciudadano->>+Sistema: Consultar estado de solicitudes
    Sistema->>+BD: Obtener solicitudes del usuario
    BD-->>-Sistema: Lista de solicitudes con estados
    Sistema-->>-Ciudadano: Mostrar estado de solicitudes
    end

    %% Caso de Uso 5: Chat de Soporte
    rect rgb(255, 230, 230)
    Ciudadano->>+Sistema: Acceder al chat de soporte
    Sistema->>Sistema: Conectar a WebSocket
    Ciudadano->>Sistema: Enviar mensaje
    Sistema->>+BD: Guardar mensaje
    BD-->>-Sistema: Mensaje guardado
    Sistema-->>Admin: Notificar nuevo mensaje
    Admin->>Sistema: Responder mensaje
    Sistema-->>-Ciudadano: Recibir respuesta en tiempo real
    end

    %% Caso de Uso 6: Gestión Administrativa
    rect rgb(240, 240, 255)
    Admin->>+Sistema: Acceder panel administrativo
    Sistema->>Sistema: Verificar permisos de admin
    Admin->>Sistema: Gestionar solicitudes
    Sistema->>+BD: Actualizar estados de solicitudes
    BD-->>-Sistema: Estados actualizados
    Sistema-->>-Admin: Confirmación de cambios
    end

    %% Caso de Uso 7: Consultar FAQs
    rect rgb(230, 230, 255)
    Ciudadano->>+Sistema: Consultar preguntas frecuentes
    Sistema->>+BD: Obtener FAQs activas
    BD-->>-Sistema: Lista de FAQs ordenadas
    Sistema-->>-Ciudadano: Mostrar preguntas frecuentes
    end
```

---

## 7. Diagrama de Estados - Ciclo de Vida de una Solicitud

```mermaid
sequenceDiagram
    participant Usuario
    participant Sistema
    participant Administrador

    Note over Usuario,Administrador: Estados de una solicitud de trámite

    %% Estado Inicial
    Usuario->>+Sistema: Crear solicitud
    Sistema->>Sistema: Estado: "pendiente"
    Sistema-->>-Usuario: Solicitud creada

    %% Transiciones de Estado
    loop Ciclo de vida de la solicitud
        alt Administrador revisa
            Administrador->>+Sistema: Revisar solicitud
            Sistema->>Sistema: Estado: "en_revision"
            Sistema-->>Usuario: Notificar: "En revisión"
            Sistema-->>-Administrador: Estado actualizado
            
            alt Documentos completos
                Administrador->>+Sistema: Aprobar solicitud
                Sistema->>Sistema: Estado: "aprobada"
                Sistema-->>Usuario: Notificar: "Aprobada"
                Sistema-->>-Administrador: Solicitud aprobada
                
                alt Procesamiento completado
                    Administrador->>+Sistema: Finalizar trámite
                    Sistema->>Sistema: Estado: "finalizada"
                    Sistema-->>Usuario: Notificar: "Finalizada"
                    Sistema-->>-Administrador: Trámite finalizado
                end
                
            else Documentos incompletos
                Administrador->>+Sistema: Solicitar documentos
                Sistema->>Sistema: Estado: "documentos_pendientes"
                Sistema-->>Usuario: Notificar: "Documentos faltantes"
                Sistema-->>-Administrador: Documentos solicitados
                
                Usuario->>+Sistema: Subir documentos
                Sistema->>Sistema: Estado: "pendiente"
                Sistema-->>-Usuario: Documentos recibidos
            end
            
        else Problemas en la solicitud
            Administrador->>+Sistema: Rechazar solicitud
            Sistema->>Sistema: Estado: "rechazada"
            Sistema-->>Usuario: Notificar: "Rechazada" + motivo
            Sistema-->>-Administrador: Solicitud rechazada
        end
        
        alt Usuario cancela
            Usuario->>+Sistema: Cancelar solicitud
            Sistema->>Sistema: Estado: "cancelada"
            Sistema-->>-Usuario: Solicitud cancelada
        end
    end
```

---

## Conclusiones Técnicas

### Características del Sistema:
- **Arquitectura MVC** con Laravel Framework
- **Sistema de autenticación** robusto con roles y permisos
- **Comunicación en tiempo real** mediante WebSockets
- **Geolocalización** para solicitudes con coordenadas GPS
- **API RESTful** para comunicación cliente-servidor
- **Base de datos relacional** bien estructurada
- **Sistema de notificaciones** para usuarios y administradores

### Tecnologías Implementadas:
- **Backend**: Laravel 10+, PHP 8+
- **Frontend**: Blade Templates, JavaScript ES6+, Bootstrap 5
- **Base de Datos**: MySQL con migraciones de Eloquent
- **Tiempo Real**: Laravel Echo, Pusher, WebSockets
- **Autenticación**: Laravel Guards, Spatie Permissions
- **Build Tools**: Vite para empaquetado de assets

### Patrones de Diseño Utilizados:
- **Repository Pattern** mediante Eloquent Models
- **Observer Pattern** con Events y Listeners
- **Middleware Pattern** para autenticación y autorización
- **MVC Pattern** para separación de responsabilidades
- **Active Record Pattern** con Eloquent ORM

---

*Diagramas generados con Mermaid.js para documentación de tesis universitaria*
*Sistema MuniApp - Gestión Municipal Digital*
