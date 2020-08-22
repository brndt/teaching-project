.PHONY: build deps composer-install keys-install composer-update composer reload test parallel-test run-tests run-parallel-tests start stop destroy doco rebuild

current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

build: deps start

deps: composer-install

# 🐘 Composer
composer-install: CMD=install
composer-update: CMD=update
composer composer-install composer-update:
	@docker run --rm --interactive --user $(id -u):$(id -g) \
		--volume $(current-dir):/app \
		--volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
		clevyr/prestissimo $(CMD) \
			--ignore-platform-reqs \
			--no-ansi \
			--no-interaction

reload:
	@docker-compose exec php-fpm kill -USR2 1
	@docker-compose exec nginx nginx -s reload

keys-install:
	mkdir -p config/jwt
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:superjwtkey
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:superjwtkey
	chmod -R 777 config/jwt

test:
	@docker exec fpm make run-tests

parallel-test:
	@docker exec fpm make run-parallel-tests

run-tests:
	mkdir -p build/test_results/phpunit
	php ./bin/console doctrine:database:create --env=test --if-not-exists
	php ./bin/console doctrine:schema:update --force --env=test
	./vendor/bin/phpunit --exclude-group='disabled' --log-junit build/test_results/phpunit/junit.xml tests
	./vendor/bin/behat --format=progress -v

run-parallel-tests:
	mkdir -p build/test_results/phpunit
	php ./bin/console doctrine:database:create --env=test --if-not-exists
	php ./bin/console doctrine:schema:update --force --env=test
	parallel --gnu -a tests.parallel || false

# 🐳 Docker Compose
start: CMD=up -d
stop: CMD=stop
destroy: CMD=down

# Usage: `make doco CMD="ps --services"`
# Usage: `make doco CMD="build --parallel --pull --force-rm --no-cache"`
doco start stop destroy:
	@docker-compose $(CMD)

rebuild:
	docker-compose build --pull --force-rm --no-cache
	make deps
	make start
