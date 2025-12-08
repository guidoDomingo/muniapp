# GUÍA DE CAPTURAS Y DIAGRAMAS PARA PRESENTACIÓN

## CAPTURAS DE PANTALLA REQUERIDAS

### 1. PÁGINA PRINCIPAL / LOGIN
**Archivo:** `screenshot_01_login.png`
**Descripción:** Pantalla de login del sistema con logo y formulario de acceso
**URL:** `http://muniapp.test/login`
**Elementos importantes:**
- Logo de la municipalidad
- Formulario de login limpio
- Enlaces de registro
- Diseño responsive

### 2. DASHBOARD ADMINISTRATIVO
**Archivo:** `screenshot_02_dashboard.png`
**Descripción:** Panel principal del administrador con estadísticas
**URL:** `http://muniapp.test/admin/dashboard`
**Elementos importantes:**
- Sidebar de AdminLTE
- Cards con estadísticas
- Gráficos de estado
- Header con usuario logueado

### 3. LISTA DE SOLICITUDES
**Archivo:** `screenshot_03_solicitudes_lista.png`
**Descripción:** Tabla de solicitudes con filtros y búsqueda
**URL:** `http://muniapp.test/admin/solicitudes`
**Elementos importantes:**
- Tabla responsive con datos
- Filtros por estado
- Barra de búsqueda
- Paginación

### 4. FORMULARIO DE CREACIÓN DE SOLICITUD
**Archivo:** `screenshot_04_crear_solicitud.png`
**Descripción:** Formulario dinámico para crear nueva solicitud
**URL:** `http://muniapp.test/solicitudes/create`
**Elementos importantes:**
- Selección de tipo de trámite
- Campos dinámicos
- Área de archivos con drag & drop
- Mapa de geolocalización

### 5. PREVIEW DE ARCHIVOS
**Archivo:** `screenshot_05_upload_preview.png`
**Descripción:** Sistema de upload con preview de imágenes
**Elementos importantes:**
- Zona de drag & drop
- Preview de imágenes subidas
- Lista de archivos con iconos
- Botones de eliminación

### 6. MAPA INTERACTIVO
**Archivo:** `screenshot_06_mapa.png`
**Descripción:** Mapa Leaflet para selección de ubicación
**Elementos importantes:**
- Mapa de OpenStreetMap
- Marcador de ubicación seleccionada
- Coordenadas mostradas
- Botones de control del mapa

### 7. DETALLE DE SOLICITUD
**Archivo:** `screenshot_07_solicitud_detalle.png`
**Descripción:** Vista completa de una solicitud individual
**URL:** `http://muniapp.test/admin/solicitudes/{id}`
**Elementos importantes:**
- Información del ciudadano
- Estado actual con colores
- Botones de acciones
- Historial de cambios

### 8. CHAT EN TIEMPO REAL
**Archivo:** `screenshot_08_chat.png`
**Descripción:** Interfaz de chat entre ciudadano y funcionario
**URL:** `http://muniapp.test/admin/chat/solicitud?room=SOL-{id}`
**Elementos importantes:**
- Mensajes en tiempo real
- Indicadores de timestamp
- Área de entrada de texto
- Información del ciudadano

### 9. GESTIÓN DE TRÁMITES
**Archivo:** `screenshot_09_tramites.png`
**Descripción:** CRUD de tipos de trámites
**URL:** `http://muniapp.test/admin/tramites`
**Elementos importantes:**
- Lista de trámites configurados
- Botones de edición
- Estado activo/inactivo
- Configuración visible

### 10. CONFIGURACIÓN DE FORMULARIO
**Archivo:** `screenshot_10_config_formulario.png`
**Descripción:** Editor de configuración de campos dinámicos
**URL:** `http://muniapp.test/admin/tramites/{id}/edit`
**Elementos importantes:**
- Editor JSON de configuración
- Preview de campos
- Validaciones configuradas
- Opciones de geolocalización

### 11. VERSIÓN MÓVIL
**Archivo:** `screenshot_11_mobile.png`
**Descripción:** Vista responsive en dispositivo móvil
**Elementos importantes:**
- Menú hamburguesa
- Cards adaptadas
- Botones táctiles
- Navegación móvil

### 12. DROPDOWN DE ACCIONES
**Archivo:** `screenshot_12_acciones.png`
**Descripción:** Menú desplegable de acciones sobre solicitudes
**Elementos importantes:**
- Dropdown funcional
- Opciones de cambio de estado
- Iconos descriptivos
- Posicionamiento correcto

---

## DIAGRAMAS TÉCNICOS REQUERIDOS

### 1. DIAGRAMA DE ARQUITECTURA DEL SISTEMA
**Archivo:** `diagram_01_arquitectura.png`
**Herramienta sugerida:** Draw.io, Lucidchart, o Figma

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     FRONTEND    │    │     BACKEND     │    │   BASE DATOS    │
│                 │    │                 │    │                 │
│ • Bootstrap 5   │◄──►│ • Laravel 10    │◄──►│ • MySQL         │
│ • JavaScript    │    │ • PHP 8+        │    │ • Migraciones   │
│ • Leaflet Maps  │    │ • Eloquent ORM  │    │ • Relaciones    │
│ • EventSource   │    │ • Middleware    │    │ • Índices       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │              ┌─────────────────┐              │
         └─────────────►│   SERVICIOS     │◄─────────────┘
                        │   EXTERNOS      │
                        │ • OpenStreetMap │
                        │ • UI-Avatars    │
                        │ • Font Awesome  │
                        └─────────────────┘
```

### 2. DIAGRAMA ENTIDAD-RELACIÓN
**Archivo:** `diagram_02_erd.png`

```sql
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    USERS    │    │  TRAMITES   │    │ SOLICITUDES │
│─────────────│    │─────────────│    │─────────────│
│ id (PK)     │    │ id (PK)     │    │ id (PK)     │
│ name        │    │ nombre      │    │ user_id (FK)│
│ email       │    │ descripcion │    │ tramite_id  │
│ password    │    │ activo      │    │ estado      │
│ created_at  │    │ config_form │    │ formulario  │
└─────────────┘    │ include_map │    │ attachments │
                   └─────────────┘    │ assigned_to │
                                     │ tracking_code│
                                     │ latitud     │
                                     │ longitud    │
                                     └─────────────┘
                                           │
                                           │
                               ┌─────────────────┐
                               │ SOLICITUD_HISTORY│
                               │─────────────────│
                               │ id (PK)        │
                               │ solicitud_id   │
                               │ user_id        │
                               │ action         │
                               │ old_value      │
                               │ new_value      │
                               │ comments       │
                               └─────────────────┘
```

### 3. DIAGRAMA DE FLUJO DE PROCESOS
**Archivo:** `diagram_03_workflow.png`

```
[CIUDADANO INICIA] → [SELECCIONA TRÁMITE] → [COMPLETA FORMULARIO]
                                                      ↓
[RECIBE TRACKING] ← [SOLICITUD CREADA] ← [SUBE DOCUMENTOS]
       ↓                                          ↓
[SEGUIMIENTO]    → [FUNCIONARIO REVISA] → [ASIGNA ESTADO]
       ↓                   ↓                     ↓
[CHAT CONSULTAS] ← [PROCESO INTERNO] → [ACTUALIZA PROGRESO]
       ↓                                          ↓
[NOTIFICACIÓN]   ← [SOLICITUD COMPLETA] ← [FINALIZA TRÁMITE]
```

### 4. DIAGRAMA DE ROLES Y PERMISOS
**Archivo:** `diagram_04_roles.png`

```
                    ┌─────────────┐
                    │    ADMIN    │
                    │ (Todos los  │
                    │  permisos)  │
                    └─────────────┘
                           │
            ┌──────────────┼──────────────┐
            │              │              │
    ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
    │ FUNCTIONARY │ │ COMMISSION  │ │    USER     │
    │             │ │             │ │ (CIUDADANO) │
    │• Gestionar  │ │• Aprobar    │ │• Crear      │
    │  solicitudes│ │  solicitudes│ │  solicitudes│
    │• Chat admin │ │• Revisar    │ │• Ver propias│
    │• Asignar    │ │• Chat       │ │• Chat       │
    └─────────────┘ └─────────────┘ └─────────────┘
```

### 5. DIAGRAMA DE COMPONENTES
**Archivo:** `diagram_05_componentes.png`

```
┌────────────────────────────────────────────────────┐
│                   MUNIAPP                          │
├────────────────────────────────────────────────────┤
│ CAPA DE PRESENTACIÓN                               │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐   │
│ │   CITIZEN   │ │    ADMIN    │ │    API      │   │
│ │   PORTAL    │ │   PANEL     │ │  ENDPOINTS  │   │
│ └─────────────┘ └─────────────┘ └─────────────┘   │
├────────────────────────────────────────────────────┤
│ CAPA DE LÓGICA DE NEGOCIO                          │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐   │
│ │ SOLICITUDES │ │   TRÁMITES  │ │    CHAT     │   │
│ │  MANAGER    │ │   MANAGER   │ │   MANAGER   │   │
│ └─────────────┘ └─────────────┘ └─────────────┘   │
├────────────────────────────────────────────────────┤
│ CAPA DE ACCESO A DATOS                             │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐   │
│ │ ELOQUENT    │ │   CACHE     │ │   FILES     │   │
│ │    ORM      │ │  SYSTEM     │ │  STORAGE    │   │
│ └─────────────┘ └─────────────┘ └─────────────┘   │
└────────────────────────────────────────────────────┘
```

---

## MOCKUPS DE DISEÑO

### 1. WIREFRAME DE FORMULARIO DINÁMICO
**Archivo:** `mockup_01_form.png`
**Descripción:** Wireframe mostrando cómo se renderizan campos dinámicos

### 2. MOCKUP DE CHAT INTERFACE
**Archivo:** `mockup_02_chat.png`
**Descripción:** Diseño del sistema de chat en tiempo real

### 3. MOCKUP RESPONSIVE
**Archivo:** `mockup_03_responsive.png`
**Descripción:** Cómo se adapta la interfaz a diferentes pantallas

---

## INSTRUCCIONES PARA CAPTURAS

### Configuración Previa
1. **Datos de prueba:** Asegúrate de tener solicitudes creadas
2. **Usuario admin:** Logueado como administrador
3. **Browser:** Chrome o Firefox en pantalla completa
4. **Resolución:** 1920x1080 para capturas desktop

### Herramientas Recomendadas
- **LightShot** para capturas rápidas
- **Snagit** para capturas con anotaciones
- **Chrome DevTools** para simular móvil
- **Figma** para crear mockups

### Lista de Verificación
- [ ] Todas las capturas tomadas
- [ ] Diagramas creados
- [ ] Mockups diseñados
- [ ] Archivos nombrados correctamente
- [ ] Calidad HD (mínimo 1280x720)
- [ ] Sin información sensible visible

---

## PRESENTACIÓN POWERPOINT

### Estructura Sugerida (35 slides)
1. **Portada** (1 slide)
2. **Agenda** (1 slide)  
3. **Introducción** (3 slides)
4. **Objetivos** (2 slides)
5. **Marco Teórico** (3 slides)
6. **Metodología** (2 slides)
7. **Análisis** (4 slides)
8. **Tecnologías** (4 slides)
9. **Desarrollo** (6 slides)
10. **Demo** (4 slides - capturas)
11. **Pruebas** (2 slides)
12. **Resultados** (2 slides)
13. **Conclusiones** (3 slides)

### Tips para la Presentación
- **Duración:** 25-30 minutos + 10 min preguntas
- **Formato:** PowerPoint (.pptx) o PDF
- **Fuente:** Arial o Calibri, mínimo 24pt
- **Colores:** Tema corporativo de la universidad
- **Imágenes:** Alta resolución, bien contrastadas

### Backup de Demostración
- **Video grabado:** 5 minutos mostrando flujo completo
- **Capturas estáticas:** Por si falla la demo en vivo
- **Datos preparados:** Solicitudes y usuarios de prueba

---

Esta guía te proporciona todo lo necesario para crear una presentación visual impactante que demuestre las capacidades técnicas y funcionales del sistema MuniApp.