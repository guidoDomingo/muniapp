# MuniApp - Diagramas Complementarios para Tesis
## Diagramas Específicos de Componentes y Procesos

---

## 8. Diagrama de Clases - Modelos del Sistema

```mermaid
classDiagram
    class User {
        +Long id
        +String name
        +String email
        +DateTime email_verified_at
        +String password
        +String remember_token
        +DateTime created_at
        +DateTime updated_at
        +isAdmin() Boolean
        +solicitudes() Collection~Solicitud~
        +chats() Collection~Chat~
        +roles() Collection~Role~
    }

    class Tramite {
        +Long id
        +String nombre
        +String descripcion
        +DateTime created_at
        +DateTime updated_at
        +solicitudes() Collection~Solicitud~
    }

    class Solicitud {
        +Long id
        +Long user_id
        +Long tramite_id
        +JSON formulario
        +String detalles
        +String comentario
        +Decimal latitud
        +Decimal longitud
        +String estado
        +DateTime created_at
        +DateTime updated_at
        +user() User
        +tramite() Tramite
    }

    class Faq {
        +Long id
        +String pregunta
        +String respuesta
        +Boolean activo
        +Integer orden
        +String categoria
        +DateTime created_at
        +DateTime updated_at
        +scopeActivo() Builder
    }

    class Chat {
        +Long id
        +Long user_id
        +String message
        +String room
        +DateTime created_at
        +DateTime updated_at
        +user() User
    }

    class Role {
        +Long id
        +String name
        +String guard_name
        +DateTime created_at
        +DateTime updated_at
        +permissions() Collection~Permission~
        +users() Collection~User~
    }

    class Permission {
        +Long id
        +String name
        +String guard_name
        +DateTime created_at
        +DateTime updated_at
        +roles() Collection~Role~
    }

    %% Relaciones
    User ||--o{ Solicitud : "tiene muchas"
    Tramite ||--o{ Solicitud : "genera muchas"
    User ||--o{ Chat : "envía muchos"
    User }o--o{ Role : "tiene roles"
    Role }o--o{ Permission : "tiene permisos"

    %% Estilos
    classDef userClass fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
    classDef businessClass fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    classDef systemClass fill:#e8f5e8,stroke:#388e3c,stroke-width:2px

    class User userClass
    class Tramite,Solicitud,Faq,Chat businessClass
    class Role,Permission systemClass
```

---

## 9. Diagrama de Componentes - Arquitectura Laravel

```mermaid
graph TB
    %% Capa de Presentación
    subgraph "Capa de Presentación"
        Views[Blade Views]
        JS[JavaScript/Vite]
        CSS[CSS/SCSS]
        Components[Componentes UI]
    end

    %% Capa de Aplicación
    subgraph "Capa de Aplicación"
        Routes[Rutas Web/API]
        Controllers[Controladores]
        Middleware[Middleware]
        FormRequests[Form Requests]
    end

    %% Capa de Dominio
    subgraph "Capa de Dominio"
        Models[Modelos Eloquent]
        Events[Eventos]
        Listeners[Listeners]
        Services[Servicios]
        Jobs[Jobs/Colas]
    end

    %% Capa de Infraestructura
    subgraph "Capa de Infraestructura"
        Database[(Base de Datos)]
        FileSystem[Sistema de Archivos]
        Cache[Cache]
        Queue[Cola de Trabajos]
        WebSockets[WebSocket Server]
    end

    %% Servicios Externos
    subgraph "Servicios Externos"
        Email[Servicio de Email]
        Maps[Google Maps API]
        Pusher[Pusher Service]
    end

    %% Conexiones
    Views --> Controllers
    JS --> Routes
    CSS --> Views
    Components --> Views

    Routes --> Controllers
    Controllers --> FormRequests
    Controllers --> Middleware
    Controllers --> Models

    Models --> Services
    Controllers --> Events
    Events --> Listeners
    Controllers --> Jobs

    Models --> Database
    Services --> FileSystem
    Services --> Cache
    Jobs --> Queue
    Listeners --> WebSockets

    Services --> Email
    Services --> Maps
    Listeners --> Pusher

    %% Estilos
    classDef presentation fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef application fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef domain fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef infrastructure fill:#fff3e0,stroke:#e65100,stroke-width:2px
    classDef external fill:#fce4ec,stroke:#c2185b,stroke-width:2px

    class Views,JS,CSS,Components presentation
    class Routes,Controllers,Middleware,FormRequests application
    class Models,Events,Listeners,Services,Jobs domain
    class Database,FileSystem,Cache,Queue,WebSockets infrastructure
    class Email,Maps,Pusher external
```

---

## 10. Diagrama de Flujo - Proceso Completo de Gestión de Trámites

```mermaid
flowchart TD
    A[Usuario accede al sistema] --> B{¿Usuario registrado?}
    
    B -->|No| C[Formulario de registro]
    C --> D[Validar datos de registro]
    D --> E[Crear cuenta de usuario]
    E --> F[Confirmar email]
    F --> G[Acceso al sistema]
    
    B -->|Sí| H{¿Credenciales válidas?}
    H -->|No| I[Mostrar error de login]
    I --> J[Reintentar login]
    J --> H
    
    H -->|Sí| G[Acceso al sistema]
    G --> K[Dashboard principal]
    
    K --> L[Consultar trámites disponibles]
    L --> M[Mostrar catálogo de trámites]
    M --> N{¿Seleccionar trámite?}
    
    N -->|No| O[Explorar otras opciones]
    O --> P{¿Usar chat de soporte?}
    P -->|Sí| Q[Acceder al chat]
    P -->|No| R[Consultar FAQs]
    
    N -->|Sí| S[Mostrar formulario del trámite]
    S --> T[Completar información requerida]
    T --> U{¿Requiere geolocalización?}
    
    U -->|Sí| V[Obtener coordenadas GPS]
    V --> W[Adjuntar ubicación]
    W --> X[Validar formulario completo]
    
    U -->|No| X[Validar formulario completo]
    X --> Y{¿Datos válidos?}
    
    Y -->|No| Z[Mostrar errores de validación]
    Z --> T
    
    Y -->|Sí| AA[Crear solicitud en BD]
    AA --> BB[Generar número de seguimiento]
    BB --> CC[Notificar al usuario]
    CC --> DD[Notificar al administrador]
    
    %% Proceso administrativo
    DD --> EE[Admin revisa solicitud]
    EE --> FF{¿Decisión administrativa?}
    
    FF -->|Aprobar| GG[Cambiar estado a 'aprobada']
    GG --> HH[Procesar trámite]
    HH --> II[Estado: 'finalizada']
    II --> JJ[Notificar finalización]
    
    FF -->|Solicitar documentos| KK[Estado: 'documentos_pendientes']
    KK --> LL[Notificar documentos faltantes]
    LL --> MM[Usuario sube documentos]
    MM --> EE
    
    FF -->|Rechazar| NN[Estado: 'rechazada']
    NN --> OO[Notificar rechazo con motivo]
    
    %% Sistema de seguimiento
    CC --> PP[Usuario puede hacer seguimiento]
    PP --> QQ[Consultar estado de solicitudes]
    QQ --> RR[Mostrar historial y estado actual]
    
    %% Flujos de finalización
    JJ --> SS[Proceso completado]
    OO --> SS
    Q --> SS
    R --> SS
    
    %% Estilos
    classDef startEnd fill:#c8e6c9,stroke:#4caf50,stroke-width:2px
    classDef process fill:#e1f5fe,stroke:#2196f3,stroke-width:2px
    classDef decision fill:#fff3e0,stroke:#ff9800,stroke-width:2px
    classDef error fill:#ffebee,stroke:#f44336,stroke-width:2px
    classDef admin fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px
    
    class A,SS startEnd
    class C,D,E,F,G,K,L,M,S,T,V,W,X,AA,BB,CC,DD,HH,II,JJ,MM,PP,QQ,RR process
    class B,H,N,P,U,Y,FF decision
    class I,Z,LL,OO error
    class EE,GG,KK,NN admin
```

---

## 11. Diagrama de Red - Infraestructura del Sistema

```mermaid
graph TB
    %% Cliente/Usuario
    subgraph "Cliente (Navegador)"
        Browser[Navegador Web]
        PWA[Progressive Web App]
    end

    %% Red y Balanceadores
    subgraph "Red y Balanceamiento"
        Internet[Internet]
        CDN[Content Delivery Network]
        LoadBalancer[Balanceador de Carga]
        Firewall[Firewall/WAF]
    end

    %% Servidores de Aplicación
    subgraph "Servidores de Aplicación"
        WebServer1[Servidor Web 1<br/>Nginx + PHP-FPM]
        WebServer2[Servidor Web 2<br/>Nginx + PHP-FPM]
        AppServer[Servidor de Aplicación<br/>Laravel]
    end

    %% Servicios de Tiempo Real
    subgraph "Servicios de Tiempo Real"
        WebSocketServer[Servidor WebSocket<br/>Laravel Reverb/Pusher]
        RedisCluster[Cluster Redis<br/>Cache + Sessions]
    end

    %% Base de Datos
    subgraph "Capa de Datos"
        DBMaster[(Base de Datos Master<br/>MySQL)]
        DBSlave[(Base de Datos Slave<br/>MySQL Read Replica)]
        FileStorage[Almacenamiento de Archivos<br/>S3/Local Storage]
    end

    %% Servicios Externos
    subgraph "Servicios Externos"
        EmailService[Servicio de Email<br/>SMTP/SES]
        MapsAPI[Google Maps API]
        MonitoringService[Servicio de Monitoreo<br/>Logs + Métricas]
    end

    %% Conexiones principales
    Browser --> Internet
    PWA --> Internet
    Internet --> CDN
    CDN --> Firewall
    Firewall --> LoadBalancer
    
    LoadBalancer --> WebServer1
    LoadBalancer --> WebServer2
    WebServer1 --> AppServer
    WebServer2 --> AppServer
    
    AppServer --> WebSocketServer
    AppServer --> RedisCluster
    WebSocketServer --> RedisCluster
    
    AppServer --> DBMaster
    AppServer -.-> DBSlave
    DBMaster --> DBSlave
    AppServer --> FileStorage
    
    AppServer --> EmailService
    AppServer --> MapsAPI
    AppServer --> MonitoringService

    %% Estilos
    classDef client fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
    classDef network fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    classDef server fill:#e8f5e8,stroke:#388e3c,stroke-width:2px
    classDef realtime fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    classDef database fill:#fce4ec,stroke:#c2185b,stroke-width:2px
    classDef external fill:#efebe9,stroke:#5d4037,stroke-width:2px

    class Browser,PWA client
    class Internet,CDN,LoadBalancer,Firewall network
    class WebServer1,WebServer2,AppServer server
    class WebSocketServer,RedisCluster realtime
    class DBMaster,DBSlave,FileStorage database
    class EmailService,MapsAPI,MonitoringService external
```

---

## 12. Diagrama de Despliegue - Entornos del Sistema

```mermaid
graph TB
    %% Entorno de Desarrollo
    subgraph "Entorno de Desarrollo"
        DevLaptop[Laptop Desarrollador]
        DevDB[(SQLite/MySQL Local)]
        DevDocker[Docker Local]
        DevTools[Herramientas de Desarrollo<br/>Vite, Artisan, PHPUnit]
    end

    %% Control de Versiones
    subgraph "Control de Versiones"
        GitHub[GitHub Repository]
        GitActions[GitHub Actions<br/>CI/CD Pipeline]
    end

    %% Entorno de Pruebas
    subgraph "Entorno de Testing/Staging"
        TestServer[Servidor de Pruebas]
        TestDB[(Base de Datos de Pruebas)]
        TestRedis[Redis Testing]
    end

    %% Entorno de Producción
    subgraph "Entorno de Producción"
        ProdServer1[Servidor Producción 1]
        ProdServer2[Servidor Producción 2]
        ProdDB[(Base de Datos Producción<br/>MySQL Cluster)]
        ProdRedis[Redis Cluster]
        ProdStorage[Almacenamiento Producción]
    end

    %% Monitoreo y Logs
    subgraph "Monitoreo y Observabilidad"
        LogServer[Servidor de Logs<br/>ELK Stack]
        MonitoringServer[Servidor de Monitoreo<br/>Grafana + Prometheus]
        AlertManager[Gestor de Alertas]
    end

    %% Flujo de desarrollo
    DevLaptop --> DevDB
    DevLaptop --> DevDocker
    DevLaptop --> DevTools
    DevLaptop --> GitHub

    %% Pipeline CI/CD
    GitHub --> GitActions
    GitActions --> TestServer
    TestServer --> TestDB
    TestServer --> TestRedis

    %% Despliegue a producción
    GitActions --> ProdServer1
    GitActions --> ProdServer2
    ProdServer1 --> ProdDB
    ProdServer2 --> ProdDB
    ProdServer1 --> ProdRedis
    ProdServer2 --> ProdRedis
    ProdServer1 --> ProdStorage
    ProdServer2 --> ProdStorage

    %% Monitoreo
    ProdServer1 --> LogServer
    ProdServer2 --> LogServer
    ProdDB --> MonitoringServer
    ProdRedis --> MonitoringServer
    LogServer --> MonitoringServer
    MonitoringServer --> AlertManager

    %% Estilos
    classDef development fill:#e8f5e8,stroke:#4caf50,stroke-width:2px
    classDef versioning fill:#e3f2fd,stroke:#2196f3,stroke-width:2px
    classDef testing fill:#fff3e0,stroke:#ff9800,stroke-width:2px
    classDef production fill:#ffebee,stroke:#f44336,stroke-width:2px
    classDef monitoring fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px

    class DevLaptop,DevDB,DevDocker,DevTools development
    class GitHub,GitActions versioning
    class TestServer,TestDB,TestRedis testing
    class ProdServer1,ProdServer2,ProdDB,ProdRedis,ProdStorage production
    class LogServer,MonitoringServer,AlertManager monitoring
```

---

## 13. Diagrama de Tiempo Real - Chat y Notificaciones

```mermaid
sequenceDiagram
    participant U1 as Usuario 1
    participant U2 as Usuario 2
    participant Admin as Administrador
    participant Frontend as Frontend App
    participant API as Laravel API
    participant WS as WebSocket Server
    participant DB as Base de Datos
    participant Queue as Cola de Trabajos
    participant Email as Servicio Email

    Note over U1,Email: Sistema completo de comunicación en tiempo real

    %% Configuración inicial
    rect rgb(240, 248, 255)
    Note over U1,WS: Establecimiento de conexiones WebSocket
    U1->>+Frontend: Acceder al chat
    Frontend->>+WS: Conectar WebSocket (canal: chat.general)
    WS-->>-Frontend: Conexión establecida
    Frontend-->>-U1: Chat disponible

    U2->>+Frontend: Acceder al chat
    Frontend->>+WS: Conectar WebSocket (canal: chat.general)
    WS-->>-Frontend: Conexión establecida
    Frontend-->>-U2: Chat disponible

    Admin->>+Frontend: Acceder panel admin + chat
    Frontend->>+WS: Conectar WebSocket (canal: chat.admin)
    WS-->>-Frontend: Conexión establecida
    Frontend-->>-Admin: Panel admin + chat disponible
    end

    %% Intercambio de mensajes
    rect rgb(248, 255, 240)
    Note over U1,DB: Usuario 1 envía mensaje
    U1->>+Frontend: Escribir mensaje
    Frontend->>+API: POST /chat (mensaje)
    API->>+DB: Guardar mensaje
    DB-->>-API: Mensaje guardado (ID: 123)
    
    API->>+WS: Broadcast NewChatMessage
    WS->>Frontend: Mensaje a canal chat.general
    Frontend-->>U2: Mostrar nuevo mensaje
    WS->>Frontend: Mensaje a canal chat.admin
    Frontend-->>Admin: Notificar nuevo mensaje
    
    API-->>-Frontend: Confirmación de envío
    Frontend-->>-U1: Mensaje enviado exitosamente
    end

    %% Sistema de notificaciones por email
    rect rgb(255, 248, 240)
    Note over API,Email: Notificaciones asíncronas
    API->>+Queue: Job: SendEmailNotification
    Queue->>+Email: Enviar email a admin
    Email-->>-Queue: Email enviado
    Queue-->>-API: Job completado
    end

    %% Respuesta del administrador
    rect rgb(248, 240, 255)
    Note over Admin,U1: Administrador responde
    Admin->>+Frontend: Responder mensaje
    Frontend->>+API: POST /chat (respuesta admin)
    API->>+DB: Guardar respuesta
    DB-->>-API: Respuesta guardada (ID: 124)
    
    API->>+WS: Broadcast AdminResponse
    WS->>Frontend: Mensaje a canal chat.general
    Frontend->>Frontend: Marcar como respuesta oficial
    Frontend-->>U1: Mostrar respuesta del admin
    Frontend-->>U2: Mostrar respuesta del admin
    
    API-->>-Frontend: Confirmación de respuesta
    Frontend-->>-Admin: Respuesta enviada
    end

    %% Notificación de nueva solicitud
    rect rgb(255, 240, 248)
    Note over U1,Email: Nueva solicitud genera notificaciones
    U1->>+Frontend: Crear nueva solicitud
    Frontend->>+API: POST /solicitudes
    API->>+DB: Crear solicitud
    DB-->>-API: Solicitud creada (ID: 456)
    
    par Notificación en tiempo real
        API->>+WS: Broadcast NewSolicitudEvent
        WS->>Frontend: Evento a canal admin
        Frontend-->>Admin: Notificar nueva solicitud
    and Notificación por email
        API->>+Queue: Job: NotifyNewSolicitud
        Queue->>+Email: Email a administradores
        Email-->>-Queue: Email enviado
        Queue-->>-API: Notificación enviada
    end
    
    API-->>-Frontend: Solicitud creada exitosamente
    Frontend-->>-U1: Confirmación de solicitud
    end

    %% Actualización de estado de solicitud
    rect rgb(240, 255, 248)
    Note over Admin,U1: Administrador actualiza solicitud
    Admin->>+Frontend: Cambiar estado solicitud
    Frontend->>+API: PUT /solicitudes/456 (estado: aprobada)
    API->>+DB: Actualizar estado
    DB-->>-API: Estado actualizado
    
    par Notificación WebSocket
        API->>+WS: Broadcast SolicitudStatusChanged
        WS->>Frontend: Evento a usuario específico
        Frontend-->>U1: Notificar cambio de estado
    and Notificación Email
        API->>+Queue: Job: NotifyStatusChange
        Queue->>+Email: Email al usuario
        Email-->>-Queue: Email enviado
        Queue-->>-API: Usuario notificado
    end
    
    API-->>-Frontend: Estado actualizado
    Frontend-->>-Admin: Confirmación de actualización
    end

    %% Manejo de desconexiones
    Note over U1,WS: Gestión de reconexiones automáticas
    U1->>Frontend: Pérdida de conexión de red
    Frontend->>Frontend: Detectar desconexión
    Frontend->>Frontend: Intentar reconexión automática
    Frontend->>+WS: Reconectar WebSocket
    WS-->>-Frontend: Reconectado exitosamente
    Frontend->>+API: Sincronizar mensajes perdidos
    API->>+DB: Obtener mensajes desde última conexión
    DB-->>-API: Mensajes no recibidos
    API-->>-Frontend: Mensajes para sincronizar
    Frontend-->>U1: Mostrar mensajes perdidos
```

---

## Métricas y KPIs del Sistema

### Métricas Técnicas:
- **Tiempo de respuesta promedio**: < 200ms para consultas básicas
- **Disponibilidad del sistema**: 99.9% uptime objetivo
- **Conexiones WebSocket concurrentes**: Hasta 1000 usuarios simultáneos
- **Throughput de base de datos**: 500 transacciones por segundo
- **Tiempo de reconexión**: < 5 segundos automático

### Métricas de Negocio:
- **Solicitudes procesadas**: Seguimiento diario/mensual
- **Tiempo promedio de resolución**: Por tipo de trámite
- **Satisfacción del usuario**: Métricas de uso del chat y FAQs
- **Adopción del sistema**: Usuarios activos vs registrados
- **Eficiencia administrativa**: Tiempo de procesamiento de solicitudes

---

*Documentación técnica completa para tesis universitaria*  
*Sistema MuniApp - Plataforma de Gestión Municipal Digital*
