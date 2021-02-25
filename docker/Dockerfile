#FROM circleci/php:7.4-cli
FROM php:7.4-cli
RUN apt-get update && apt-get install -y libuv1-dev libevent-dev libssl-dev git
RUN pecl install channel://pecl.php.net/uv-0.2.4
RUN docker-php-ext-enable uv
RUN docker-php-ext-install sockets
RUN pecl install event
RUN docker-php-ext-enable event
RUN pecl install ev
RUN docker-php-ext-enable ev

RUN mkdir ~/lib && \
    cd ~/lib && \
    git clone https://github.com/expressif/pecl-event-libevent.git && \
    cd pecl-event-libevent && \
    phpize && \
    ./configure && \
    make && \
    make install

RUN docker-php-ext-enable libevent