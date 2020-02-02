CURRENT_UID ?=
ARGS ?=

.PHONY: phar
phar:
	php ./bin/console --env=prod cache:warmup; \
	php -d phar.readonly=false tools/box.phar compile --config=box.json

tools/phive.phar:
	wget -O tools/phive.phar https://phar.io/releases/phive.phar; \
	wget -O tools/phive.phar.asc https://phar.io/releases/phive.phar.asc; \
	gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79; \
	gpg --verify tools/phive.phar.asc tools/phive.phar; \
	chmod +x tools/phive.phar

.PHONY: install-phive
install-phive: tools/phive.phar

.PHONY: setup
setup: install-phive
	docker-compose run --rm --entrypoint=/usr/local/bin/php phpdoc tools/phive.phar install --copy --trust-gpg-keys 4AA394086372C20A,D2CCAC42F6295E7D,E82B2FB314E9906E,8E730BA25823D8B5,D0254321FB74703A --force-accept-unsigned

.PHONY: phpcs
phpcs:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest -s ${ARGS}

.PHONY: phpcbf
phpcbf:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest phpcbf ${ARGS}

.PHONY: phpstan
phpstan:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpstan-ga:latest analyse src --no-progress --configuration phpstan.neon ${ARGS}

.PHONY: test
test:
	docker run -it --rm -v${CURDIR}:/github/workspace phpdoc/phpunit-ga
	docker run -it --rm -v${CURDIR}:/data -w /data php:7.2 -f ./tests/coverage-checker.php 69

.PHONY: phpunit
phpunit: test

.PHONY: integration-test
integration-test: node_modules/.bin/cypress build/default/index.html build/clean/index.html
	docker run -it --rm -v ${CURDIR}:/e2e -w /e2e cypress/included:3.2.0

.PHONY: behat
behat:
	docker-compose run --rm behat ${ARGS}

.PHONY: composer-require-checker
composer-require-checker:
	docker run -it --rm -v${PWD}:/data -w /data  phpga/composer-require-checker-ga check --config-file /data/composer-require-config.json composer.json

.PHONY: pre-commit-test
pre-commit-test: test phpcs phpstan composer-require-checker

.PHONY: shell
shell:
	CURRENT_UID=$(shell id -u):$(shell id -g) docker-compose run --rm --entrypoint=/bin/bash phpdoc

node_modules/.bin/cypress:
	docker run -it --rm -v ${CURDIR}:/opt/phpdoc -w /opt/phpdoc node npm install

build/default/index.html: data/examples/MariosPizzeria/**/*
	CURRENT_UID=$(shell id -u):$(shell id -g) docker-compose run --rm phpdoc --config=data/examples/MariosPizzeria/phpdoc.xml --template=default --target=build/default

build/clean/index.html: data/examples/MariosPizzeria/**/*
	CURRENT_UID=$(shell id -u):$(shell id -g) docker-compose run --rm phpdoc --config=data/examples/MariosPizzeria/phpdoc.xml --template=clean --target=build/clean
