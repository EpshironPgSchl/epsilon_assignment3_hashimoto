FROM php:8.3-fpm

EXPOSE 5173

ENV TZ Asia/Tokyo

# ミドルウェアインストール
RUN apt-get update && \
	apt-get install -y git unzip libzip-dev libicu-dev libonig-dev nodejs npm && \
	docker-php-ext-install intl pdo_mysql zip

COPY ./docker/app/php.ini /usr/local/etc/php/php.ini

# Composerインストール
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ユーザーを作成
ARG DOCKER_UID=1000
ARG DOCKER_USER=docker
ARG DOCKER_PASSWORD=docker
ARG ROOT_USER=root
ARG ROOT_PASSWORD=root
RUN useradd -m --uid ${DOCKER_UID} --groups sudo ${DOCKER_USER} \
    && echo ${DOCKER_USER}:${DOCKER_PASSWORD} | chpasswd \
    && echo ${ROOT_USER}:${ROOT_PASSWORD} | chpasswd

# 作成したユーザーに切り替える
USER ${DOCKER_USER}