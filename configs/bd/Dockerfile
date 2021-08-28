FROM postgres:13.4

ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN localedef -i es_ES -c -f UTF-8 -A /usr/share/locale/locale.alias es_ES.utf8
ENV LANG es_ES.utf8

COPY postgresql.conf /var/lib/postgresql/data/postgresql.conf
COPY script.sql /docker-entrypoint-initdb.d/script.sql

CMD ["postgres"]