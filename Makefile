COMPOSE_DEV=./dev/docker-compose.yml
COMPOSE_NAME=bnt
COMPOSE_ENV=./dev/.env

up:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) up --build -d
down:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) down
build:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) build --progress=plain
php:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) exec web bash
mysql:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) exec db mysql -uroot -proot
test-unit:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) exec web php dev/phpunit.phar --configuration dev/phpunit.xml --testsuite Unit 
test-func:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) exec web php dev/phpunit.phar --configuration dev/phpunit.xml --testsuite Functional
test-intg:;docker compose -p $(COMPOSE_NAME) -f $(COMPOSE_DEV) --env-file $(COMPOSE_ENV) exec web php dev/phpunit.phar --configuration dev/phpunit.xml --testsuite Integration 
