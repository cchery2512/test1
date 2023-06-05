FROM php:8.2-cli

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo y copiar los archivos del proyecto
WORKDIR /Users/carmelochery/Desktop/symfony/test1
COPY . /Users/carmelochery/Desktop/symfony/test1

# Ejecutar el comando de instalación de dependencias
RUN composer install

# Configurar el comando para el cron job
ENV CRON_COMMAND="/Users/carmelochery/Desktop/symfony/test1/bin/console app:currency:rates EUR USD GBP ARS MXN PAB COL CRC COP AED BRL BOB >> /Users/carmelochery/Desktop/symfony/test1/log/crontab.log 2>&1"

CMD cron && tail -f /var/log/cron.log