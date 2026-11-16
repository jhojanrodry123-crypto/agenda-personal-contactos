# Agenda Personal de Contactos

Aplicación web académica desarrollada con **PHP, MySQL, HTML y CSS**. Permite registrar usuarios, iniciar y cerrar sesión y administrar una agenda privada de contactos.

## Funcionalidades

- Registro de usuarios con validación y correo único.
- Contraseñas protegidas con `password_hash()` y verificadas con `password_verify()`.
- Inicio de sesión con sesiones PHP y regeneración del identificador de sesión.
- Panel privado con datos del usuario y sus contactos.
- Crear, consultar, editar y eliminar contactos.
- Consultas preparadas con PDO.
- Protección CSRF en formularios que modifican información.
- Validación en el servidor y escape de contenido al mostrarlo.
- Cada consulta de edición y eliminación comprueba el `id_usuario` propietario.

## Tecnologías

- PHP 8.0 o superior (se recomienda PHP 8.1+).
- MySQL 5.7+ o MariaDB compatible.
- Servidor Apache (por ejemplo, XAMPP).
- HTML5 y CSS3.

## Requisitos

1. Instalar XAMPP u otro entorno con Apache, PHP y MySQL.
2. Tener habilitadas las extensiones PHP `pdo_mysql` y `mbstring`.
3. Iniciar Apache y MySQL.

## Instalación local con XAMPP

1. Copia la carpeta `agenda_personal_php` dentro de `C:\xampp\htdocs\`.
2. Abre `http://localhost/phpmyadmin` en tu navegador.
3. Importa el archivo `database.sql` desde la pestaña **Importar**. El script crea la base de datos `agenda_personal` y sus tablas.
4. Revisa `config/database.php`. Para una instalación XAMPP típica, los valores por defecto son: host `127.0.0.1`, base `agenda_personal`, usuario `root` y contraseña vacía. Si tu MySQL usa otras credenciales, define las variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`.
5. Abre `http://localhost/agenda_personal_php/`.
6. Registra una cuenta, inicia sesión y prueba las operaciones de contactos.

## Estructura

```text
agenda_personal_php/
├── assets/
│   └── style.css
├── config/
│   └── database.php
├── includes/
│   ├── bootstrap.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── contact_create.php
├── contact_delete.php
├── contact_edit.php
├── dashboard.php
├── database.sql
├── index.php
├── login.php
├── logout.php
├── register.php
└── README.md
```

## Seguridad y privacidad

- La contraseña nunca se guarda en texto plano; se almacena el hash generado por `password_hash()`.
- La autenticación usa `password_verify()`.
- El identificador de sesión se regenera después de autenticar al usuario.
- Las páginas privadas llaman a `require_login()`.
- Las operaciones de contacto filtran por el identificador del usuario autenticado; conocer o cambiar el ID de la URL no permite modificar registros ajenos.
- Se usan consultas preparadas PDO, validación del lado del servidor, escape HTML y tokens CSRF.
- Para publicar en un servidor real, configura HTTPS, credenciales seguras y permisos adecuados de la base de datos. No publiques contraseñas de producción en el repositorio.

## Pruebas recomendadas

1. Registra dos usuarios diferentes.
2. Con el primer usuario crea, edita y elimina un contacto.
3. Cierra sesión e inicia con el segundo usuario; comprueba que no aparece el contacto del primero.
4. Copia la URL de edición de un contacto del primer usuario y ábrela con la sesión del segundo. El sistema debe negar la operación y volver al panel.
5. Prueba iniciar sesión con una contraseña incorrecta y registrar nuevamente un correo existente.

## Publicación en GitHub

1. Crea un repositorio público llamado `agenda-personal-contactos`.
2. Sube el contenido de esta carpeta, incluyendo `database.sql` y este `README.md`.
3. No subas credenciales reales ni datos personales de prueba.
4. Copia la dirección del repositorio para incluirla en la entrega del aula virtual.

---

**Nota académica:** Antes de entregar, ejecuta y prueba la aplicación en tu computador y adapta la presentación para explicar con tus propias palabras lo que observaste en funcionamiento.
