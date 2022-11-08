# Gift Cabinet

Simple PHP + MYSQL + VueJS based dockerized application

# Features

- Authorized users can ask a gift by button press
- If they don't like it it's possible to refuse one gift and ask for another
- It is tree gift's types:
  - money
  - loyalty points
  - physical gift (ex. Book)
- User can convert money or physical gift to loyalty points
- Money and physical gifts is limited, but points are not
- Process not processed money payments with console command
- Bearer token auth
- Logs

# Getting started

To run project manually:

Create your "giftdb" MYSQL database. then:

```
cd backend
composer install
composer up
composer migrate-and-seed
composer start

cd ../frontend
yarn (or npm install)
yarn serve (or npm run serve)

```

You can go to your http://localhost:3333/ and have a fun

Or just running via docker compose:

```
cd backend
docker compose up --build -d
docker exec backend composer migrate-and-seed

```

And go to http://0.0.0.0

---

Migrates create several users you can use accounts:

login: user1@user.com
password: qwerty1

login: user2@user.com
password: qwerty2

...

login: user5@user.com
password: qwerty5

---

## Testing

To run tests (from backend dir):

```
composer test
```

To run tests with coverage checking

```
composer test-coverage
```

### Load Testing

```
cd backend
docker compose up --build -d
docker exec backend composer migrate-and-seed

docker run --network=backend_app-network \
  --rm skandyla/wrk -t6 -c10 -d15s -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOjEsInJvbGUiOiJ1c2VyIiwiYWN0aW9uIjoiYXV0aDphY2Nlc3MiLCJleHAiOjE3Njc4OTI1ODB9.WeC3Ync76gDcKjcDirNBT0fkwjm_wchoV1uYxgrnwUU' http://webserver/api/account

```

- Bearer token can be different

---

## Concole payments

When user confirm a money gift it creates a new payment with a bank request.
But you can process such payments from your side via console command from backend dirrectory

```
php bin/console payment n

```

where n - number of payments you wish to process
