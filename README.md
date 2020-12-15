Commande pour installation de postgreSQL

docker run -d -p 5432:5432 -e POSTGRES_USER=test -e POSTGRES_PASSWORD=test -e POSTGRES_DB=postgres --name postgres13 postgres:13