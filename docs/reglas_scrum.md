# Definition of Done (DoD)

Este documento establece los criterios obligatorios que toda historia de usuario o tarea debe cumplir para ser considerada como "Terminada". Ninguna tarea puede pasar a la columna "Done" en Jira si no cumple con la totalidad de los siguientes puntos.

## 1. Criterios de Código y Diseño (Calidad del Producto)
* **Respeto a la arquitectura:** El código nuevo respeta la estructura de carpetas establecida en el repositorio (separación clara entre frontend y backend / MVC).
* **Estándares de código:** El código está indentado correctamente, utiliza nombres de variables descriptivos y no contiene código comentado innecesario o "basura".
* **Seguridad en Base de Datos:** Todas las consultas y transacciones a la base de datos utilizan PDO y Prepared Statements. Queda estrictamente prohibida la concatenación directa de variables en consultas SQL.

## 2. Criterios de Pruebas (Evidencia de QA)
* **Pruebas locales exitosas:** El desarrollador ha probado la funcionalidad en su entorno local (localhost) y verifica que cumple exactamente con los Criterios de Aceptación definidos en la Historia de Usuario.
* **Evidencia de calidad (QA):** Se ha generado evidencia de que la funcionalidad opera sin errores. Esto se demuestra mediante un script de prueba automatizado en Katalon o, en su defecto, mediante un documento de pruebas adjunto con capturas de pantalla/video que valide los flujos de éxito y error.

## 3. Criterios de Integración y Control de Versiones (Git)
* **Commits descriptivos:** El código fue subido (pushed) al repositorio en GitHub utilizando mensajes de commit claros que hagan referencia a la funcionalidad trabajada. Siempre respetando la estructura de los Conventional Commits, "tipo(ámbito o caracteristica): descripción del commit"
* **Integración en la rama correcta:** El código ha sido fusionado (merged) en la rama de integración correspondiente main sin generar conflictos que rompan el sistema. Por otro lado, cada rama independiente debe de aprobarse y cumplir con los criterios de calidad antes de fusionarse a la rama main. 
* **Validación de entorno:** La funcionalidad se ejecuta correctamente al descargar y levantar el proyecto completo desde el repositorio, garantizando que no faltan archivos locales del desarrollador.

## 4. Criterios de Gestión y Documentación (Scrum)
* **Actualización del tablero:** La tarea o historia de usuario ha sido movida a la columna "Done" en Jira por el desarrollador responsable.
* **Documentación sincronizada:** Si la funcionalidad implicó la creación de nuevas tablas en la base de datos o cambios estructurales, los diagramas y diccionarios de datos ubicados en la carpeta `/docs` del repositorio han sido actualizados.

## Definition of Ready (Definición de Preparado)

Este documento establece los requisitos que debe cumplir una Historia de Usuario (o tarea) en el Product Backlog para que el equipo de desarrollo acepte incluirla en un Sprint durante la reunión de Sprint Planning. Si una historia no cumple con estos puntos, no está "lista" para ser trabajada.

### Criterios de Aceptación para iniciar una tarea:
* **Estructura clara:** La historia de usuario está redactada en el formato estándar ("Como [rol], quiero [acción], para [beneficio]") y su propósito es comprensible para todo el equipo.
* **Criterios de Aceptación definidos:** La historia incluye una lista puntual y verificable de lo que el sistema debe hacer para considerar que la función hace lo que se pide.
* **Estimación asignada:** El equipo de desarrollo ya revisó la historia, resolvió sus dudas con el Product Owner y le asignó una estimación de esfuerzo o complejidad.
* **Dependencias resueltas:** La historia no está bloqueada por el trabajo de otro equipo o tarea técnica pendiente. 
* **Recursos disponibles:** Si la tarea requiere un diseño previo (como un prototipo en papel, diagrama o wireframe), este ya se encuentra adjunto en Jira o en el repositorio.

## Flujo de Trabajo y Uso de Herramientas

Para garantizar la transparencia y el seguimiento del proyecto, el equipo acuerda el siguiente flujo de trabajo integrando Jira y GitHub.

### Gestión de Tareas (Jira)
El tablero de Jira es la fuente de la verdad sobre el avance del Sprint. Las columnas del tablero y su uso son:

* **To Do (Por hacer):** Tareas que cumplen con el DoR y fueron seleccionadas para el Sprint actual. Ninguna tarea entra aquí si no fue aprobada en el Sprint Planning.
* **In Progress (En progreso):** El desarrollador asignado está trabajando activamente en el código. 
  * *Regla:* Un desarrollador no debe tener más de una tarea en "In Progress" al mismo tiempo para no generar cuellos de botella.
* **Testing / QA (En pruebas):** El desarrollo de la funcionalidad terminó y se está generando la evidencia de pruebas locales (Katalon o capturas).
* **Done (Terminado):** La tarea cumple con todos los puntos del Definition of Done (DoD).

### Control de Versiones (GitHub)
Dado que el historial de commits será evaluado por el Stakeholder, se seguirán estas reglas:

* **Ramas de trabajo:** Nadie programa directamente en la rama `main`. Cada historia de usuario de Jira se trabajará en una rama independiente (ejemplo: `feature/login-usuario`).
* **Sincronización:** Una vez que el código pasa las pruebas (columna Testing en Jira), se hace la fusión (merge) a la rama de integración.