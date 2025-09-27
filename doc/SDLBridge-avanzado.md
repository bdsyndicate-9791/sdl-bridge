# SDL Bridge  
## SQL seguro, trazable y libre para WordPress

---

## Página 1: El problema estructural en WordPress

WordPress ha revolucionado la creación de sitios web, pero su relación con las bases de datos sigue siendo precaria. Aunque ofrece una API llamada `$wpdb` para ejecutar consultas SQL, esta interfaz está lejos de ser suficiente para entornos profesionales. El acceso a datos en WordPress suele estar acoplado directamente al código PHP, lo que genera una serie de problemas estructurales que se agravan a medida que el proyecto crece.

El primer problema es el **acoplamiento excesivo** entre lógica de negocio y presentación. Las consultas SQL se escriben directamente en funciones PHP, sin separación clara de responsabilidades. Esto dificulta la reutilización, la auditoría y la evolución del sistema. El segundo problema es la **falta de validación formal**: los parámetros se interpolan sin control tipado, lo que abre la puerta a errores silenciosos y vulnerabilidades como inyecciones SQL. El tercero es la **ausencia de trazabilidad**: no hay forma de saber qué consulta se ejecutó, con qué parámetros, en qué contexto y por quién.

Además, este modelo ignora por completo el rol del DBA. En entornos profesionales, el DBA es responsable de diseñar, optimizar y proteger la base de datos. Pero en WordPress, el desarrollador suele escribir SQL sin coordinación, sin control, sin respeto por la arquitectura de datos. Esto genera fricción, errores y pérdida de calidad técnica.

**SDL Bridge** nace como respuesta a esta fractura. No es un plugin. No es un ORM. Es un motor declarativo que permite que el DBA escriba SQL puro, y que el desarrollador lo consuma sin tocarlo. Cada archivo `.sdl` encapsula una consulta segura, parametrizada, auditable. Cada ejecución es controlada, validada, trazable. SDL Bridge no busca reemplazar nada. Busca restaurar la dignidad del SQL en WordPress.

---

## Página 2: El modelo declarativo y su implementación

SDL Bridge introduce un modelo declarativo para el acceso a datos en WordPress. En lugar de escribir SQL directamente en PHP, el DBA define archivos `.sdl` que contienen consultas SQL puras con variables tipadas. Estas variables están delimitadas por `{{ }}` y especifican el tipo de dato esperado, como `int`, `datetime`, `uuid`, entre otros. Esto permite que el motor SDL Bridge valide cada parámetro antes de ejecutar la consulta, evitando errores y vulnerabilidades.

Ejemplo de archivo `.sdl`:

```sql
SELECT * FROM wp_orders
WHERE store_id = {{store_id:int}}
  AND created_at >= {{start:datetime}}
```

Desde el lado del desarrollador, el flujo es simple y formal:

```php
SDLBridge::report('orders.sdl')
    ->withParams([
        'store_id' => 42,
        'start' => '2023-01-01'
    ])
    ->execute();
```

El motor compila la consulta, valida los parámetros, ejecuta con seguridad y devuelve un objeto estructurado con los siguientes campos:

- `rows`: los datos devueltos por la consulta
- `sql`: la consulta compilada
- `params`: los parámetros usados
- `errors`: validaciones fallidas, si las hay

Este modelo permite desacoplar completamente la lógica SQL del código PHP. El DBA entrega un `.sdl`, el frontend lo consume. Sin fricción. Sin riesgo. Sin ambigüedad. Además, el motor permite inspeccionar la consulta compilada antes de ejecutarla, lo que facilita la auditoría y el debugging.

SDL Bridge no impone estructura. Permite que tú la definas. Puedes organizar tus archivos `.sdl` por módulo, por tipo de operación, por cliente o por entorno. Puedes versionarlos, documentarlos, auditarlos. Puedes extender el motor para agregar nuevos tipos, validaciones personalizadas, logs, métricas. Es una herramienta quirúrgica para quienes valoran la arquitectura limpia.

---

## Página 3: Seguridad, trazabilidad y extensión real

Uno de los pilares de SDL Bridge es la seguridad. Al validar cada parámetro por tipo antes de interpolarlo en la consulta, se elimina el riesgo de inyección SQL por diseño. No hay concatenaciones, no hay SQL dinámico, no hay interpolación directa. El motor utiliza `$wpdb->prepare()` internamente, pero con control total sobre cada paso del proceso.

La trazabilidad es otro aspecto clave. Cada archivo `.sdl` puede ser versionado, auditado, documentado. Puedes saber qué consulta se ejecutó, con qué parámetros, en qué contexto, en qué momento. Esto es esencial para entornos que manejan datos sensibles, como comercio electrónico, sistemas institucionales o plataformas SaaS. Además, el motor permite registrar cada ejecución, lo que facilita la generación de métricas, reportes y alertas.

La extensibilidad está en el núcleo de SDL Bridge. Los tipos de datos (`int`, `datetime`, `uuid`, `email`, etc.) son clases que pueden ser extendidas. Puedes definir nuevos tipos heredando de `SDLType`, agregando validaciones personalizadas, formatos específicos, reglas de negocio. También puedes extender el motor para agregar nuevas interfaces, como integración con REST API, shortcodes, cron jobs, CLI, etc.

La portabilidad es otro punto fuerte. SDL Bridge no depende de librerías externas, no requiere configuraciones complejas, no impone frameworks. Es compatible con PHP 7.4+ y WordPress 5.0+. Puede integrarse en cualquier flujo técnico, comercial o institucional. Puedes usarlo en plugins, temas, sistemas internos, paneles administrativos, APIs públicas.

SDL Bridge no es una solución mágica. Es una herramienta quirúrgica para quienes construyen software serio. Si tu producto merece seguridad, trazabilidad y claridad, SDL Bridge es tu estándar.

---

## Página 4: Adopción estratégica y cultura declarativa

SDL Bridge está diseñado para equipos que valoran la separación de roles, la trazabilidad y la seguridad. No es una herramienta para todos. Es una herramienta para quienes entienden que el acceso a datos debe ser formal, controlado, auditable.

¿Quién debería usarlo?

- Agencias que trabajan con datos sensibles y necesitan auditar cada consulta.
- SaaS que requieren control total sobre la lógica de acceso a datos.
- DBAs que exigen respeto por el SQL y quieren entregar lógica limpia.
- Desarrolladores que prefieren consumir lógica declarativa sin tocar SQL.

¿Quién debería evitarlo?

- Plugins que acoplan lógica SQL directamente en PHP.
- Equipos sin cultura de desacoplamiento ni roles definidos.
- Proyectos que prefieren soluciones mágicas sin trazabilidad.

¿Cómo adoptarlo?

1. Define tu estructura de archivos `.sdl`.
2. Formaliza los tipos que usarás (`int`, `datetime`, `slug`, etc.).
3. Entrena a tu equipo en el flujo: el DBA escribe, el frontend consume.
4. Integra el motor en tu stack: `SDLBridge::report()->withParams()->execute()`.
5. Documenta cada `.sdl` como unidad técnica y editorial.

La adopción de SDL Bridge no es solo técnica. Es cultural. Implica reconocer que el SQL merece respeto. Que el DBA tiene un rol. Que la trazabilidad no es opcional. Que la seguridad no se improvisa.

SDL Bridge no compite con ORMs ni plugins mágicos. Se posiciona como una herramienta declarativa para quienes construyen software serio. Si tu producto merece claridad, seguridad y trazabilidad, SDL Bridge es tu puente.

**Bienvenido al nuevo mundo declarativo.**  
**Bienvenido a SDL Bridge.**
