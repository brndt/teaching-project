current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

.PHONY: build
build: deps keys-install start

.PHONY: deps
deps: composer-install

.PHONY: composer-install composer-update
composer-install: CMD=install
composer-update: CMD=update
composer-install composer-update:
	@docker run --rm --interactive --user $(id -u):$(id -g) \
		--volume $(current-dir):/app \
		--volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
		composer:2 $(CMD) \
			--ignore-platform-reqs \
			--no-ansi \
			--no-scripts \
			--no-interaction

.PHONY: reload
reload:
	@docker-compose exec php-fpm kill -USR2 1
	@docker-compose exec nginx nginx -s reload

.PHONY: keys-install
keys-install:
	mkdir -p config/jwt
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:superjwtkey
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:superjwtkey
	chmod -R 777 config/jwt

.PHONY: test
test:
	@docker exec fpm make run-tests

.PHONY: parallel-test
parallel-test:
	@docker exec fpm make run-parallel-tests

.PHONY: run-tests
run-tests:
	mkdir -p build/test_results/phpunit
	php ./bin/console doctrine:database:create --env=test --if-not-exists
	php ./bin/console doctrine:schema:update --force --env=test
	./vendor/bin/phpunit --exclude-group='disabled' --log-junit build/test_results/phpunit/junit.xml tests
	./vendor/bin/behat --format=progress -v

.PHONY: run-parallel-tests
run-parallel-tests:
	mkdir -p build/test_results/phpunit
	php ./bin/console doctrine:database:create --env=test --if-not-exists
	php ./bin/console doctrine:schema:update --force --env=test
	parallel --gnu -a tests.parallel || false

.PHONY: start stop destroy
start: CMD=up -d
stop: CMD=stop
destroy: CMD=down

start stop destroy:
	@docker-compose $(CMD)

.PHONY: rebuild
rebuild:
	docker-compose build --pull --force-rm --no-cache
	make deps
	make start
