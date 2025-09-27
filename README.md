# 📚 Documentación Técnica: SDL Bridge

> **Versión 1.0**  
> *Un puente seguro entre DBAs y desarrolladores en entornos WordPress*

---

## 🎯 Propósito

Permitir a los **DBAs** escribir consultas SQL complejas en archivos externos (con soporte para variables), y a los **desarrolladores** ejecutarlas de forma segura en WordPress, **sin acoplamiento directo**, **sin inyección SQL**, y con **máxima flexibilidad de despliegue**.

SDL Bridge actúa como capa de abstracción que:
- Lee archivos `.sdl` escritos por DBAs.
- Valida y sanitiza parámetros según tipos definidos.
- Genera SQL seguro compatible con `$wpdb`.
- Ejecuta consultas y devuelve resultados estructurados.

---

## 🧱 Arquitectura General

```
sdl-bridge/
├── src/                          # Núcleo del motor (PSR-4)
│   ├── Types/                    # Sistema de tipos extensible
│   ├── TypeRegistry.php          # Registro central de tipos
│   ├── SDLParser.php             # Analiza archivos .sdl
│   ├── SDLCompiler.php           # Compila SDL → SQL seguro
│   ├── ReportEngine.php          # Punto de entrada
│   └── exceptions/               # Excepciones del dominio
├── bootstrap.php                 # Autoloader y alias global
└── (informes .sdl van fuera de esta carpeta)
```

---

## 🔌 Integración con WordPress

### 1. Incluir la librería
```php
// En tu plugin o tema
require_once WP_PLUGIN_DIR . '/sdl-bridge/bootstrap.php';
```

### 2. Ejecutar un informe
```php
$results = SDLBridge::report('abandoned_cart')
    ->withParams([
        'start' => '2024-01-01 00:00:00',
        'end'   => '2024-02-01 00:00:00'
    ])
    ->execute();

// $results->rows: array de resultados
// $results->sql: consulta SQL generada (para debugging)
```

> ✅ El alias global `SDLBridge` está disponible tras incluir `bootstrap.php`.

---

## 📂 Ubicación de Informes (`.sdl`)

El sistema busca los informes en este orden de prioridad:

### 1. **Configuración explícita** (recomendado para producción)
```php
// En wp-config.php (fuera de DOCUMENT_ROOT)
define('SDL_BRIDGE_REPORTS_DIR', '/ruta/segura/fuera/de/la/web/reports');
```

### 2. **Carpeta en el home del usuario** (webhosts compartidos)
```
/home/tuusuario/sdl-bridge/reports/
```
> ✅ Fuera de `public_html/`, `htdocs/`, etc.

### 3. **Dentro del plugin** (solo desarrollo)
```
/wp-content/plugins/tu-plugin/reports/
```
> ⚠️ Se añade `.htaccess` con `Deny from all`, pero no es 100% seguro.

---

## 🧪 Formato de Archivo: `.sdl`

- Es un **superconjunto de SQL estándar**.
- Soporta **variables** con sintaxis: `{{nombre:tipo}}`.
- Si no hay variables, se trata como SQL puro.

### Ejemplo
```sql
SELECT product_id, SUM(qty) AS total
FROM wp_wtf_session_events
WHERE event_timestamp >= {{start:datetime}}
  AND event_timestamp < {{end:datetime}}
  AND store_id = {{store_id:int}}
GROUP BY product_id
```

---

## 🔐 Sistema de Tipos

### Tipos Base Incluidos
| Tipo        | Validación                              | Ejemplo de uso       |
|-------------|------------------------------------------|----------------------|
| `string`    | Escapa con `$wpdb->_real_escape()`       | `'O\'Reilly'`        |
| `int`       | Solo enteros válidos                     | `42`                 |
| `float`     | Números decimales                        | `3.1415`             |
| `bool`      | `1` / `0`                                | `1`                  |
| `date`      | Formato `YYYY-MM-DD`                     | `'2024-05-01'`       |
| `datetime`  | Formato `YYYY-MM-DD HH:MM:SS`            | `'2024-05-01 12:00:00'` |
| `array`     | Array → `(val1,val2,...)`                | `(1,2,3)`            |

### Extensibilidad
Para añadir un nuevo tipo (ej: `uuid`):

```php
// 1. Crear clase
class UuidType extends BaseType { ... }

// 2. Registrar
$engine = new \SDLBridge\ReportEngine($reportsDir);
$engine->registerType('uuid', new UuidType());

// 3. Usar en SDL
// WHERE user_id = {{user_id:uuid}}
```

---

## 🛡️ Seguridad

- **Ningún valor entra sin validación y sanitización**.
- **Todos los tipos usan mecanismos de WordPress** (`$wpdb->_real_escape()`).
- **Variables de tipo `identifier` (nombres de tabla/columna) están prohibidas** por defecto (riesgo alto).
- **Los informes deben estar fuera de `DOCUMENT_ROOT`** (recomendado).

---

## 🧩 Excepciones del Sistema

| Excepción                     | Cuándo se lanza                                  |
|------------------------------|--------------------------------------------------|
| `SDLException`               | Error genérico en SDL (variables faltantes, etc.) |
| `TypeNotFoundException`       | Tipo no registrado (ej: `{{fecha:fechota}}`)     |

> ✅ Ambas extienden `\RuntimeException` y pertenecen al namespace `SDLBridge\exceptions`.

---

## 📦 Autoloader

- **PSR-4 compatible**.
- Usa `__DIR__` de `bootstrap.php` como raíz.
- **No requiere Composer**.
- **Resistente a inclusiones desde cualquier contexto**.

---

## 🧪 Ejemplo Completo

### Archivo: `~/sdl-bridge/reports/abandoned_cart.sdl`
```sql
WITH abandoned_sessions AS (
    SELECT DISTINCT session_header_id
    FROM wp_wtf_session_events
    WHERE event_type = 'add_to_cart'
      AND event_timestamp >= {{start:datetime}}
      AND event_timestamp < {{end:datetime}}
      AND session_header_id NOT IN (
          SELECT DISTINCT session_header_id
          FROM wp_wtf_session_events
          WHERE event_type = 'checkout_initialized'
            AND event_timestamp >= {{start:datetime}}
            AND event_timestamp < {{end:datetime}}
      )
),
-- ... resto de la consulta (igual que en el ejemplo original)
```

### Código PHP
```php
$results = SDLBridge::report('abandoned_cart')
    ->withParams([
        'start' => '2024-05-01 00:00:00',
        'end'   => '2024-06-01 00:00:00'
    ])
    ->execute();

foreach ($results->rows as $row) {
    echo $row['product_name'] . ": " . $row['veces_agregado_en_carritos_abandonados'] . "\n";
}
```

---

## 📌 Requisitos

- **PHP 7.4+**
- **WordPress 5.0+**
- **MySQL 5.6+** (por funciones como `COALESCE`, `NULLIF`)

---

## 🧭 Filosofía de Diseño

1. **Desacoplamiento**: DBAs escriben SDL, desarrolladores solo llaman.
2. **Seguridad primero**: sanitización por tipo, no por esperanza.
3. **Extensibilidad sin tocar el core**: nuevos tipos = nuevas clases.
4. **Flexibilidad de despliegue**: desde webhosts compartidos hasta datacenters blindados.
5. **Transparencia**: siempre se puede ver la SQL generada.

---

## 📝 Licencia

> SDL Bridge es una obra de ingeniería abierta.  
> Puedes usarlo, modificarlo y distribuirlo,  
> siempre que **respetes el espíritu de claridad y responsabilidad** con el que fue creado.

---
