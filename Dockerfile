# php:8.3-apache - current supported PHP branch (8.2 goes security-only Dec 2026).
# Debian-based rather than alpine because there is no official php:*-apache
# alpine variant; the app needs mod_php under Apache.
FROM php:8.5-apache

# Pull the Debian package set up to current. The php:8.3-apache base ships 10
# fixable Critical/High OS CVEs; this clears all of them (and ~190 findings
# overall). Paired with a scheduled rebuild this is what keeps the image clean.
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y --no-install-recommends; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# The php:*-apache base ships the FULL build toolchain (build-essential, gcc,
# libc6-dev, linux-libc-dev) so consumers can run docker-php-ext-install at
# runtime. This app compiles nothing, so all of it is dead weight - and
# linux-libc-dev is a large share of the estate's remaining fixable
# Critical/High findings.
RUN set -eux; \
    apt-get purge -y --auto-remove \
        build-essential dpkg-dev gcc g++ cpp make libc6-dev linux-libc-dev \
        autoconf pkg-config binutils; \
    rm -rf /var/lib/apt/lists/* /usr/local/lib/php/build /usr/src/php.tar.xz*

# move our source code to the default path for apache
COPY ./src /var/www/html

# Create the static directory (black.jpg is served from there during blackout
# hours) and the mount point for the host image share.
RUN set -eux; \
    mkdir -p /var/www/html/static /var/www/html/images; \
    cp /var/www/html/black.jpg /var/www/html/static/black.jpg

# ENV Variables for our script and set some defaults
ENV delayinsecs=30

# expose the default 80 port for Apache, map this to whatever when running docker container
#
# Deliberately still port 80 and a root Apache master (workers drop to www-data):
# this is a published image whose documented contract is "-p <host>:80" plus a
# bind mount at /var/www/html/images. Forcing a non-root USER would both break
# every existing "-p 8181:80" run command and make host-owned mounted images
# unreadable. Raising this is a follow-up that has to change the README and the
# Home Assistant tablet configs at the same time.
EXPOSE 80

# busybox/coreutils are already present; no package installed for this check.
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1/fetch_images.php") === false ? 1 : 0);'

CMD ["apache2-foreground"]
