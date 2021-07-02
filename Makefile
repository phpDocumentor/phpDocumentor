ARGS ?=

.USER = CURRENT_UID=$(shell id -u):$(shell id -g)
.DOCKER_COMPOSE_RUN = ${.USER} docker-compose run --rm

.PHONY: help
help:
	@echo "      _           ____                                        _              ";
	@echo " _ __ | |__  _ __ |  _ \  ___   ___ _   _ _ __ ___   ___ _ __ | |_ ___  _ __ ";
	@echo "| '_ \| '_ \| '_ \| | | |/ _ \ / __| | | | '_ ' _ \ / _ \ '_ \| __/ _ \| '__|";
	@echo "| |_) | | | | |_) | |_| | (_) | (__| |_| | | | | | |  __/ | | | || (_) | |   ";
	@echo "| .__/|_| |_| .__/|____/ \___/ \___|\__,_|_| |_| |_|\___|_| |_|\__\___/|_|   ";
	@echo "|_|         |_|                                                              ";
	@echo "";
	@echo "Available commands:";
	@echo "";
	@echo "== Setup ==";
	@echo "setup            - Installs phive and all phar-based dependencies";
	@echo "install-phive    - Installs phive, the phar.io package manager.";
	@echo "pull-containers  - pulls all development docker containers";
	@echo "shell            - starts the phpDocumentor docker container and opens a terminal";
	@echo "";
	@echo "== Automated testing ==";
	@echo "pre-commit-test  - runs all checks needed before committing";
	@echo "";
	@echo "test             - runs unit tests and checks the code coverage percentage";
	@echo "unit-test        - runs unit tests";
	@echo "integration-test - runs integration tests";
	@echo "functional-test  - runs functional tests";
	@echo "e2e-test         - runs phpDocumentor and verifies the output using Cypress";
	@echo "composer-require-checker - checks for any missing composer packages";
	@echo "";
	@echo "== Code Quality ==";
	@echo "lint             - performs all linting on the code";
	@echo "phpcs            - performs code-style checks";
	@echo "phpcbf           - fixes most code-style issues";
	@echo "phpstan          - performs static analysis on the codebase using phpstan";
	@echo "psalm            - performs static analysis on the codebase using psalm";
	@echo "";
	@echo "== Release tools ==";
	@echo "phar             - Creates the PHAR file";
	@echo "docs			    - Creates local docs docker image";
	@echo "";

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
	docker-compose run --rm --entrypoint=/usr/local/bin/php phpdoc tools/phive.phar install --copy --trust-gpg-keys 4AA394086372C20A,D2CCAC42F6295E7D,E82B2FB314E9906E,8E730BA25823D8B5,D0254321FB74703A,8A03EA3B385DBAA1 --force-accept-unsigned

.PHONY: pull-containers
pull-containers:
	docker pull phpdoc/phpcs-ga
	docker pull phpdoc/phpstan-ga
	docker pull phpdoc/phpunit-ga
	docker pull php:7.2
	docker pull node

.PHONY: phpcs
phpcs:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest -s ${ARGS}

.PHONY: phpcbf
phpcbf:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpcs-ga:latest phpcbf ${ARGS}

.PHONY: phpstan
phpstan:
	docker run -it --rm -v${CURDIR}:/opt/project -w /opt/project phpdoc/phpstan-ga:latest analyse src tests --configuration phpstan.neon ${ARGS}

.PHONY: psalm
psalm:
	docker run -it --rm -v${CURDIR}:/data -w /data php:7.3 ./tools/psalm

.PHONY: lint
lint: phpcs

.PHONY: test
test: unit-test
	docker run -it --rm -v${CURDIR}:/data -w /data php:7.2 -f ./tests/coverage-checker.php 70

.PHONY: unit-test
unit-test: SUITE=--testsuite=unit

.PHONY: integration-test
integration-test: SUITE=--testsuite=integration --no-coverage

.PHONY: functional-test
functional-test: SUITE=--testsuite=functional --no-coverage

unit-test integration-test functional-test:
	docker run -it --rm -v${CURDIR}:/github/workspace phpdoc/phpunit-ga $(SUITE) $(ARGS)

.PHONY: e2e-test
e2e-test: node_modules/.bin/cypress build/default/index.html build/clean/index.html
	docker run -it --rm -v ${CURDIR}:/e2e -w /e2e cypress/included:6.6.0

.PHONY: composer-require-checker
composer-require-checker:
	${.DOCKER_COMPOSE_RUN} --entrypoint=./tools/composer-require-checker phpdoc  check --config-file /opt/phpdoc/composer-require-config.json composer.json

.PHONY: pre-commit-test
pre-commit-test: test phpcs phpstan psalm composer-require-checker

.PHONY: shell
shell:
	${.DOCKER_COMPOSE_RUN} --entrypoint=/bin/bash phpdoc

node_modules/.bin/cypress:
	docker run -it --rm -v ${CURDIR}:/opt/phpdoc -w /opt/phpdoc node npm install

build/default/index.html: data/examples/MariosPizzeria/**/*
	${.DOCKER_COMPOSE_RUN} phpdoc --config=data/examples/MariosPizzeria/phpdoc.xml --template=default --target=build/default --force

build/clean/index.html: data/examples/MariosPizzeria/**/*
	${.DOCKER_COMPOSE_RUN} phpdoc --config=data/examples/MariosPizzeria/phpdoc.xml --template=clean --target=build/clean --force

.PHONY: docs
docs:
	${.DOCKER_COMPOSE_RUN} phpdoc --setting="guides.enabled=true"

.PHONY: build-website
build-website: demo docs
	cp -p --update ./data/xsd/phpdoc.xsd ./build/website/
	cp -p -r --update ./data/website/* ./build/website
	docker build -t phpdocumentor/phpdocumentor/phpdoc.org:local ./build/website

.PHONY: demo
demo:
	${.DOCKER_COMPOSE_RUN} phpdoc --template=default --setting="guides.enabled=true" -t ./build/website/demo/default
	${.DOCKER_COMPOSE_RUN} phpdoc --template=clean -t ./build/website/demo/clean
