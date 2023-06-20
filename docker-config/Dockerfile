# We use the official PHP image with Apache
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the contents of our project to the working directory
COPY . .

# Install necessary dependencies
RUN apt-get update && \
    apt-get install -y cron libpq-dev && \
    docker-php-ext-install pdo pdo_mysql

# Configure Apache
COPY ./docker-config/my-apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && \
    service apache2 restart

# Ensure that the files are owned by www-data
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Run Composer
RUN composer install

# Configure cron to run the Symfony command every minute
RUN (crontab -l ; echo "0 1 * * * cd /var/www/html && /usr/local/bin/php bin/console app:currency:rates EUR USD GBP ARS MXN PAB COL CRC COP AED BRL") | crontab

# Set execution permission for the entrypoint script
RUN chmod +x ./docker-config/entrypoint.sh

# Set the default entry point command
CMD ["apache2-foreground"]

# Executes every time the project is started
ENTRYPOINT ["./docker-config/entrypoint.sh"]
