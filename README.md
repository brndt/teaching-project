Para configurar el proyecto se debe ejecutar los siguientes comandos:

1. cd src
2. composer install
3. sudo mkdir -p config/jwt
4. sudo openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
5. sudo openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
6. sudo chmod -R 777 config/jwt
7. docker-compose up -d
8. entrar en el contenedor de php-fpm y ejecutar "bin/console doctrine:schema:update --force" para actualizar la estructura de las tablas de base de datos o generarlas.

Las peticiones se pueden hacer gracias al fichero del Postman.
