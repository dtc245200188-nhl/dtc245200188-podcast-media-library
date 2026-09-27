FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        gd \
        intl \
        mbstring

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Create non-root user
RUN addgroup -g 1000 appgroup \
    && adduser -u 1000 -G appgroup -s /bin/sh -D appuser

# Configure PHP-FPM pool for non-root user
RUN sed -i 's/^user = .*/user = appuser/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^group = .*/group = appgroup/' /usr/local/etc/php-fpm.d/www.conf \
    && echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY --chown=appuser:appgroup . /var/www/html/

# Create uploads directory with proper permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R appuser:appgroup /var/www/html

# Switch to non-root user
USER appuser

EXPOSE 9000

CMD ["php-fpm"]
