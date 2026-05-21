# Buho Bank - Sistema Bancario Web

![Version](https://img.shields.io/badge/Versi%C3%B3n-1.4.0-blue.svg)
![Estado](https://img.shields.io/badge/Estado-Producci%C3%B3n-brightgreen)
![Metodologia](https://img.shields.io/badge/Metodolog%C3%ADa-Scrum-orange)

Buho Bank es una plataforma bancaria web desarrollada bajo la arquitectura MVC (Modelo-Vista-Controlador) utilizando PHP puro y MySQL. Este proyecto fue construido siguiendo estrictamente la Metodologia Agil Scrum, simulando un entorno de trabajo profesional mediante el uso de Sprints, Historias de Usuario (HU) y un control de versiones colaborativo.

---

## Equipo de Desarrollo (Scrum Team)

El proyecto fue construido colaborativamente por el equipo, donde cada integrante fue responsable del desarrollo, commits y pruebas de sus respectivas Historias de Usuario:

* Mario Guadarrama- Product Owner
* Ian Tranquilino - Scrum Master 
* Cristian Zapata Desarrollador 
* Marco - Desarrollador 
* Axel Ramos -  Desarrollador 
* Chochua Bautista - Desarrollador 
* Michelle Castro - Desarrollador 

---

## Flujo de Trabajo y Colaboracion (Git Flow)

Para demostrar la trazabilidad del codigo y evitar conflictos, el equipo implemento un flujo de trabajo profesional en Git:

1. main (Produccion): Contiene unicamente versiones estables liberadas al final de cada Sprint (v1.1.0, v1.2.0, v1.3.0, v1.4.0).
2. testing (Pruebas): Rama de pre-produccion donde se integraron las Historias de Usuario para verificar que no rompieran el sistema antes de la entrega final.
3. develop (Desarrollo): Rama principal de integracion diaria.
4. Ramas de Caracteristicas (feature/HU-X): Cada desarrollador creo una rama aislada para trabajar su Historia de Usuario, realizando commits atomicos y descriptivos antes de hacer un Pull Request hacia develop.

---

## Evolucion del Proyecto (Documentacion de Sprints)

El desarrollo se dividio en 4 Sprints estrategicos. A continuacion se detalla la construccion incremental del producto:

### Sprint 1: Cimientos y Autenticacion (Release v1.1.0)
Objetivo: Establecer la base de datos, la arquitectura MVC y el control de acceso seguro para los clientes.
* HU1 - Registro de Usuarios: Implementacion de encriptacion password_hash() para almacenamiento seguro de contraseñas.
* HU2 - Inicio de Sesion (Login): Validacion de credenciales y generacion de variables de sesion seguras.
* HU4 - Dashboard del Cliente: Creacion de la vista principal leyendo el saldo real de la base de datos vinculada al ID del usuario.

### Sprint 2: El Motor Financiero (Release v1.2.0)
Objetivo: Darle vida al banco permitiendo la manipulacion segura del capital mediante transacciones.
* HU9 - Depositos: Logica para incrementar el saldo de una cuenta validando montos positivos.
* HU10 - Retiros: Implementacion de validaciones de fondos suficientes para evitar saldos negativos.
* HU11 - Transferencias a Terceros: Construccion de transacciones ACID en MySQL (uso de commit y rollback) para garantizar que el dinero se descuente del origen y sume al destino simultaneamente, o falle por completo si hay un error.

### Sprint 3: Transparencia y Reportes (Release v1.3.0)
Objetivo: Proveer al cliente un control total sobre su historial financiero.
* HU13 - Historial de Transacciones: Consulta con INNER JOIN para mostrar envios y recepciones ordenados por fecha.
* HU14 - Detalle de Transaccion: Vista en profundidad de cada movimiento con comprobantes visuales.
* HU17 - Filtros de Busqueda: Implementacion de filtros por fechas y tipo de movimiento directamente en la consulta SQL.
* HU20 - Exportacion a CSV: Generacion dinamica de reportes descargables en formato Excel manipulando los headers de HTTP en PHP.

### Sprint 4: Seguridad Extrema y Back-Office (Release v1.4.0)
Objetivo: Proteger el sistema contra ataques y crear un entorno de control para el personal del banco.
* HU5 - Recuperacion de Contraseña: Sistema de generacion de claves temporales con reseteo de bloqueos.
* HU24 - Proteccion contra Fuerza Bruta: Bloqueo automatico de cuentas tras 3 intentos fallidos de login.
* HU25 & HU27 - Dashboard Administrativo: Portal exclusivo (con login independiente) que muestra estadisticas globales del banco (usuarios totales, capital retenido, transacciones).
* HU26 - Control de Acceso (Soft Delete): Capacidad del administrador para desactivar o bloquear manualmente cuentas sospechosas sin eliminar sus registros financieros.

---

## Stack Tecnologico

* Frontend: HTML5, CSS3, JavaScript, Materialize CSS.
* Backend: PHP 8+ (Programacion Orientada a Objetos, Arquitectura MVC).
* Base de Datos: MySQL (Uso de PDO para sentencias preparadas previniendo Inyecciones SQL).

---

## Ceremonias Scrum Implementadas

Para garantizar la entrega continua de valor y la sincronizacion del equipo, ejecutamos las siguientes ceremonias a lo largo de las semanas de desarrollo:

* Sprint Planning: Al inicio de cada Sprint, el Product Owner (Mario) y el equipo priorizaron el Product Backlog. Se asignaron los Story Points a cada Historia de Usuario (HU) utilizando la sucesion de Fibonacci y se definio el Sprint Goal.
* Daily Stand-ups: Sincronizaciones breves para responder: ¿Que hice ayer?, ¿Que hare hoy? y ¿Tengo algun impedimento? (El Scrum Master, Ian, facilito la resolucion de bloqueos tecnicos como conflictos en Git).
* Sprint Review: Demostracion del software funcional al final de cada iteracion, desplegando la rama de testing para validar el cumplimiento de los requerimientos.
* Sprint Retrospective: Analisis de oportunidades de mejora. Por ejemplo, mejorar la resolucion de conflictos (Merge Conflicts) al integrar codigo en la rama develop.

---

## Definicion de Terminado (Definition of Done - DoD)

Ninguna Historia de Usuario fue movida a la columna de "Terminado" (Done) en nuestro tablero sin antes cumplir con los siguientes criterios de calidad:

1. El codigo cumple con la arquitectura MVC establecida.
2. Se utilizaron sentencias preparadas (PDO) para evitar inyecciones SQL.
3. El codigo fue probado localmente por el desarrollador.
4. Se realizo un Pull Request (PR) hacia la rama develop.
5. El codigo fue revisado e integrado sin conflictos.
6. La funcionalidad opera correctamente en la rama de testing.

---

## Estructura de Directorios (Arquitectura MVC)

El proyecto mantiene una separacion estricta de responsabilidades para facilitar la escalabilidad y el trabajo en paralelo de los desarrolladores:

/buhobank
├── /backend
│   ├── /config       (Conexion a BD)
│   ├── /controllers  (Logica de negocio e intercepcion de peticiones POST/GET)
│   └── /models       (Clases, consultas SQL y reglas de negocio)
├── /frontend
│   ├── /css          (Estilos y framework Materialize)
│   ├── /js           (Interactividad del cliente)
│   └── /pages        (Vistas PHP y renderizado de datos)
└── README.md         (Documentacion del proyecto)

---

## Medidas de Seguridad (Cybersecurity)

Al ser una simulacion bancaria, el equipo priorizo la seguridad del sistema implementando estandares de la industria:

* Proteccion contra Inyeccion SQL: Uso estricto de PDO (PHP Data Objects) con bindParam() en todas las interacciones con la base de datos.
* Hashing de Credenciales: Las contraseñas nunca se almacenan en texto plano; se utiliza el algoritmo BCRYPT nativo de PHP.
* Prevencion de Fuerza Bruta: Sistema de bloqueo temporal de cuentas que excede los 3 intentos fallidos de inicio de sesion (HU24).
* Aislamiento de Sesiones: Variables de sesion independientes para clientes y administradores, previniendo la escalada de privilegios.

---

## Instalacion y Despliegue Local

Para evaluar este proyecto en un entorno local (XAMPP / MAMP / LAMP):

1. Clona este repositorio en la carpeta htdocs (o /var/www/html) usando el comando:
   git clone <URL_DEL_REPOSITORIO> buhobank

2. Importa la base de datos:
   * Abre phpMyAdmin.
   * Crea una base de datos llamada proyecto_victor.
   * Importa el archivo SQL proporcionado por el equipo que contiene la estructura (USUARIOS, CUENTAS_BANCARIAS, TRANSACCIONES, ADMINISTRADORES).

3. Configura las credenciales:
   * Navega a backend/config/database.php.
   * Verifica que el usuario (root) y la contraseña coincidan con tu entorno local.

4. Acceso al sistema:
   * Portal Clientes: http://localhost/buhobank/frontend/pages/login.php
   * Portal Administrativo: http://localhost/buhobank/frontend/pages/admin_login.php

---

## Esquema de Base de Datos (Modelo Relacional)

El sistema esta respaldado por una base de datos relacional en MySQL diseñada para garantizar la integridad referencial y la trazabilidad de las operaciones financieras. El esquema consta de 4 tablas principales:

* USUARIOS: Almacena la informacion personal de los clientes, credenciales encriptadas (password_hash) y banderas de seguridad (estado activo, bloqueos temporales e intentos fallidos de login).
* CUENTAS_BANCARIAS: Relacionada uno a muchos (1:N) con la tabla de usuarios. Maneja el saldo actual de manera estricta para evitar inconsistencias financieras.
* TRANSACCIONES: Tabla transaccional central. Registra el historial inmutable de cada movimiento (depositos, retiros y transferencias), vinculando la cuenta de origen, la cuenta de destino, el monto exacto y la marca de tiempo (timestamp).
* ADMINISTRADORES: Tabla aislada logicamente para el personal del banco. Garantiza que el acceso al Back-Office (Panel Administrativo) este completamente separado de la tabla de clientes, previniendo escalada de privilegios.

Las relaciones implementan llaves foraneas (Foreign Keys) con restricciones para evitar la eliminacion accidental de registros que corrompan el historial contable del banco.