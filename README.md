# Agenda Personal de Contactos

Aplicación web académica desarrollada con PHP, MySQL, HTML y CSS. Permite registrar usuarios, iniciar y cerrar sesión y administrar una agenda privada de contactos.

## Mejoras de esta versión

1. **Búsqueda de contactos:** permite buscar por nombre, correo electrónico o ciudad. La búsqueda se realiza únicamente dentro de los contactos del usuario autenticado.
2. **Ordenamiento:** permite ordenar los contactos alfabéticamente por nombre o por ciudad.
3. **Compatibilidad:** la función de redirección usa el tipo `void` para mantener compatibilidad con PHP 8.0 de XAMPP.

## Funcionalidades existentes

- Registro de usuarios con validación y correo único.
- Contraseñas protegidas con `password_hash()` y verificadas con `password_verify()`.
- Inicio de sesión con sesiones PHP y regeneración del identificador de sesión.
- Panel privado con datos del usuario y sus contactos.
- Crear, consultar, editar y eliminar contactos.
- Consultas preparadas con PDO.
- Protección CSRF en formularios que modifican información.
- Validación del lado del servidor y escape de contenido al mostrarlo.
- Las operaciones de edición y eliminación verifican el `id_usuario` propietario.

## Tecnologías

- PHP 8.0 o superior.
- MySQL 5.7+ o MariaDB compatible.
- Apache (por ejemplo, XAMPP).
- HTML5 y CSS3.

## Requisitos

- Apache y MySQL activos.
- Extensiones PHP `pdo_mysql` y `mbstring` habilitadas.

## Instalación local con XAMPP

1. Descarga el repositorio desde GitHub y coloca la carpeta del proyecto dentro de `C:\xampp\htdocs\`.
2. Inicia Apache y MySQL desde XAMPP.
3. Abre `http://localhost/phpmyadmin`.
4. Para una instalación nueva, importa `database.sql`. El script crea la base `agenda_personal` y las tablas `usuarios` y `contactos`.
5. Revisa `config/database.php`. Para XAMPP típico usa host `127.0.0.1`, base `agenda_personal`, usuario `root` y contraseña vacía.
6. Abre `http://localhost/agenda-personal-contactos/` (ajusta el nombre de la ruta si la carpeta extraída tiene otro nombre).
7. Registra una cuenta e inicia sesión.

### Actualizar una base existente sin perder contactos

No elimines la base de datos ni vuelvas a importar `database.sql` sobre una base que ya contiene información. Si deseas agregar los índices de búsqueda, abre phpMyAdmin, selecciona `agenda_personal`, entra en SQL y ejecuta una sola vez `actualizar_indices_busqueda.sql`. Si MySQL indica que los índices ya existen, no vuelvas a ejecutar el script.

## Mejoras en el panel

- Escribe un nombre, un correo o una ciudad en el cuadro de búsqueda y pulsa **Aplicar**.
- Selecciona **Nombre (A-Z)** o **Ciudad (A-Z)** para ordenar los resultados.
- Pulsa **Limpiar** para volver a mostrar todos los contactos.
- El sistema informa cuántos resultados se muestran y cuántos contactos tiene el usuario en total.

## Seguridad y privacidad

- Las contraseñas se almacenan como hash y se verifican con `password_verify()`.
- Las páginas privadas requieren una sesión activa.
- Se utilizan consultas preparadas PDO, validación del lado del servidor, escape HTML y tokens CSRF.
- La búsqueda, edición y eliminación se limitan al identificador del usuario autenticado.
- Los criterios de ordenamiento se seleccionan mediante una lista permitida; no se incorpora directamente a SQL ningún valor arbitrario del navegador.

## Pruebas recomendadas

1. Registra dos usuarios diferentes.
2. Con el primer usuario agrega varios contactos con distintos nombres, correos y ciudades.
3. Busca por un nombre, un correo y una ciudad.
4. Ordena por nombre y por ciudad.
5. Limpia los filtros y comprueba que vuelven a aparecer todos tus contactos.
6. Inicia sesión con el segundo usuario y verifica que no se muestran los contactos del primero.
7. Prueba editar o eliminar un contacto que pertenezca a otra cuenta; la operación debe ser denegada.

## Capturas para la actividad

Guarda capturas reales de la aplicación en la carpeta `capturas/` y súbelas al repositorio. Se recomienda incluir:

- `01-login.png`: inicio de sesión.
- `02-panel.png`: panel con contactos.
- `03-busqueda.png`: búsqueda por nombre, correo o ciudad.
- `04-ordenamiento.png`: lista ordenada por ciudad o nombre.

No incluyas contraseñas, información personal real ni credenciales de hosting.

## Despliegue en hosting

Cuando se publique la aplicación, reemplaza el texto **Pendiente de despliegue** por la URL pública:

**URL pública:** Pendiente de despliegue.

En el hosting configura `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` según los datos que entregue el panel del proveedor. No publiques esos valores privados en GitHub. Verifica que el proveedor soporte PHP, MySQL y las extensiones `pdo_mysql` y `mbstring`.

## Repositorio

Repositorio GitHub: https://github.com/jhojanrodry123-crypto/agenda-personal-contactos
