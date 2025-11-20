Web Personal II - Symfony Blog

Autor: Jordi Porcar Muñoz
Curso: Desarrollo de Aplicaciones Web - IES El Caminàs

Este proyecto es un blog personal desarrollado con Symfony 6 y Bootstrap. Incluye gestión de usuarios, login/registro, creación de posts, formulario de contacto y control de acceso al blog.

Requisitos

PHP >= 8.1
Composer
Servidor web (Apache/Nginx) o symfony server:start

Instalación

Clonar el repositorio: git clone https://github.com/ieselcaminas/web-personal-ii-thejordix03.git
cd web-personal-ii-thejordix03/my_new_project
Instalar compose install
Crear el archivo .env.local basado en .env si es necesario y configurar la base de datos:cp .env .env.local

Levantar el servidor de desarrollo:  php -S 127.0.0.1:8000 -t public
