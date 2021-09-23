FROM php:7.4-alpine
RUN set -eux; \
    apk --no-cache -Uu add \
    acl=~2.2 \
    curl=~7.78 \
    fcgi=~2.4 \
    file=~5.40 \
    gettext=~0.21 \
    git=~2.32 \
    gnu-libiconv=~1.16 \
    py3-setuptools=~52.0

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev=~67.1 \
    libxml2-dev=~2.9 \
    libzip-dev=~1.7.3 \
    zlib-dev=~1.2 \
	; \
	\
    docker-php-source extract; \
    docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    zip \
	; \
	docker-php-ext-enable \
    opcache; \
    \
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps; \
    docker-php-source delete

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

ADD ./composer.json .
ADD ./package.json .

EXPOSE 9000
