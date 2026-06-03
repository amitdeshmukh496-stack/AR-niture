FROM php:8.2-apache

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-enabled/index.conf

EXPOSE 80

CMD ["apache2-foreground"]
