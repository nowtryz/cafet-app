FROM mysql/mysql-server:5.7.21

ENV MYSQL_ROOT_PASSWORD mecatro
ENV MYSQL_DATABASE mecatronesql
ENV MYSQL_USER mecatro
ENV MYSQL_PASSWORD mecatro

# copy sql scripts and renamed them to be executed in alphabetical order
COPY docker/mysql/config.sql /docker-entrypoint-initdb.d/a-config.sql
COPY docker/mysql/users.sql /docker-entrypoint-initdb.d/b-users.sql
COPY docker/mysql/structure.sql /docker-entrypoint-initdb.d/c-structure.sql
COPY docker/mysql/demo.sql /docker-entrypoint-initdb.d/d-demo.sql

RUN sed -Ei 's/^(bind-address|log)/#&/' /etc/my.cnf