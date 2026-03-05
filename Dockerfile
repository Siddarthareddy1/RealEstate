FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install mysqli extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set the working directory to the application code
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Make the uploads directory writable
RUN mkdir -p uploads && chown -R www-data:www-data uploads && chmod -R 755 uploads

# Expose port 80
EXPOSE 80
