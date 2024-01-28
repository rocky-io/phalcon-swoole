FROM rockyio/phalcon-swoole:phalcon5.4-swoole5.1-php8.2-debian

ENV TIMEZONE="Asia/Shanghai"
RUN set -ex \
    && { \
    echo "upload_max_filesize=128M"; \
    echo "post_max_size=128M"; \
    echo "memory_limit=512M"; \
    echo "date.timezone=${TIMEZONE}"; \
    } | tee /usr/local/etc/php/conf.d/99_override.ini \
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone

#RUN composer install --no-dev -o
#ENTRYPOINT ["php", "./bin/phalconswoole", "start"]
