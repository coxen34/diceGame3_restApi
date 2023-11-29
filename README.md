<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# diceGame3_restApi

## Descripción

Este proyecto es una aplicación RESTful API basada en Laravel que implementa un juego de dados. Los usuarios pueden registrarse para jugar, y se les asigna automáticamente el rol de jugador. La aplicación tiene funcionalidades específicas según el rol del usuario, con acciones permitidas para administrador y jugadores.



## Requisitos

- PHP >= 7.4
- Composer >= 2.6.5
- Laravel/Framework ^10.10
- Laravel/Passport ^11.10
- Spatie/Laravel-Permission ^6.1


## Instalación
1. Clona el repositorio: `git clone https://github.com/tu-usuario/nombre-repositorio.git`
2. Instala las dependencias: `composer install`
3. Configura tu archivo `.env` con las credenciales de la base de datos.
4. Ejecuta las migraciones: `php artisan migrate`
5. Genera las claves de Passport: `php artisan passport:install`
6. Ejecuta el servidor de desarrollo: `php artisan serve`
### Uso

- Regístrate como usuario para participar en el juego.
- Se te asignará automáticamente el rol de jugador.
- Las acciones permitidas varían según el rol:
  - **Para el rol admin (nombre: admin, contraseña: admin, email: admin@mailto.com):**
    - Ver la lista de jugadores con sus tiradas y el porcentaje de éxito de cada uno.
    - Borrar las partidas de un usuario.
    - Ver el mejor y peor jugador.

## Funcionalidades

- Implementación de un juego de dados.
- Registro de usuarios con asignación automática del rol de jugador.
- Acciones específicas para administradores y jugadores.
- Lista de jugadores con estadísticas de tiradas y porcentaje de éxito.
- Borrado de partidas de usuarios.
- Estadísticas de mejor y peor jugador.
- Validación de contraseña: mínimo 9 dígitos, una letra mayúscula, un número y al menos un signo.


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
