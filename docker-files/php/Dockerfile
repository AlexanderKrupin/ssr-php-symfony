FROM ubuntu:bionic
ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get -o Acquire::ForceIPv4=true update && \
    apt-get install -y --no-install-recommends \
    git subversion make curl php-cli php-fpm php-dev wget bzip2 php-pear php-pgsql php-mbstring php-curl php-zip gnupg2

RUN apt-get install -y software-properties-common
RUN printf "deb http://ppa.launchpad.net/stesie/libv8/ubuntu xenial main\ndeb-src http://ppa.launchpad.net/stesie/libv8/ubuntu xenial main" >> /etc/apt/sources.list.d/stesie-libv8.list

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys 1A10946ED858A0DF
RUN apt-get --allow-unauthenticated update && \
    apt-get --allow-unauthenticated install -y libv8-7.5 libv8-7.5-dev g++ expect

RUN git config --global http.sslverify false && git clone https://github.com/phpv8/v8js.git /usr/local/src/v8js && \
    cd /usr/local/src/v8js && phpize && ./configure --with-v8js=/opt/libv8-7.5 && \
    export NO_INTERACTION=1 && make all -j4 && make test install

RUN echo extension=v8js.so > /etc/php/7.2/fpm/conf.d/99-v8js.ini
RUN echo extension=v8js.so > /etc/php/7.2/cli/conf.d/99-v8js.ini

RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/7.2/cli/php.ini
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/7.2/fpm/php.ini
RUN sed -i "s/display_errors = Off/display_errors = On/" /etc/php/7.2/fpm/php.ini
RUN sed -i "s/upload_max_filesize = .*/upload_max_filesize = 10M/" /etc/php/7.2/fpm/php.ini
RUN sed -i "s/post_max_size = .*/post_max_size = 12M/" /etc/php/7.2/fpm/php.ini
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.2/fpm/php.ini

RUN sed -i -e "s/pid =.*/pid = \/var\/run\/php7.2-fpm.pid/" /etc/php/7.2/fpm/php-fpm.conf
RUN sed -i -e "s/error_log =.*/error_log = \/proc\/self\/fd\/2/" /etc/php/7.2/fpm/php-fpm.conf
RUN sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.2/fpm/php-fpm.conf
RUN sed -i "s/listen = .*/listen = 9000/" /etc/php/7.2/fpm/pool.d/www.conf
RUN sed -i "s/;catch_workers_output = .*/catch_workers_output = yes/" /etc/php/7.2/fpm/pool.d/www.conf

RUN wget -O /usr/bin/composer https://getcomposer.org/composer.phar --no-check-certificate \
    && chmod +x /usr/bin/composer

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm7.2"]
