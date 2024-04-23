# Use an official PHP runtime as a parent image
FROM php:8.3-fpm

# Expose port 9000 to connect to the Nginx server
EXPOSE 9200

# Set the working directory to /var/www/html
WORKDIR /var/www/laravel/

# A wildcard is used to ensure both package.json AND package-lock.json are copied
COPY package*.json ./

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy the Laravel application files into the container
COPY . /var/www/laravel/

# Set the ownership of the files to the PHP-FPM user
RUN chown -R www-data:www-data /var/www/laravel/

# Start PHP-FPM
CMD ["php-fpm"]
