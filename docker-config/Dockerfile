# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Copiamos el contenido de nuestro proyecto al directorio de trabajo
COPY . .

# Instalamos las dependencias necesarias
RUN apt-get update && \
    apt-get install -y cron libpq-dev && \
    docker-php-ext-install pdo pdo_mysql

# Configuramos Apache
COPY ./docker-config/my-apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && \
    service apache2 restart

# Aseguramos que los archivos son propiedad de www-data
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Instalación de Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Ejecución de Composer
RUN composer install

# Configuración de cron para ejecutar el comando de Symfony cada minuto
RUN (crontab -l ; echo "0 1 * * * cd /var/www/html && /usr/local/bin/php bin/console app:currency:rates EUR USD GBP ARS MXN PAB COL CRC COP AED BRL") | crontab

# The entry point execution permission
RUN chmod +x ./docker-config/entrypoint.sh

# The default entry point command
CMD ["apache2-foreground"]

# Executes every time the project is started
ENTRYPOINT ["./docker-config/entrypoint.sh"]