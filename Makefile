.PHONY: phar
phar:
	php ./bin/console --env=prod cache:warm; \
	php -d phar.readonly=false tools/box.phar compile --config=box.json

.PHONY: install-phive
install-phive:
	wget -O tools/phive.phar https://phar.io/releases/phive.phar; \
	wget -O tools/phive.phar.asc https://phar.io/releases/phive.phar.asc; \
	gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79; \
	gpg --verify tools/phive.phar.asc tools/phive.phar; \
	chmod +x tools/phive.phar

.PHONY: setup
setup: install-phive
	docker build -t phpdoc/dev docker/with-xdebug; \
	docker run -it --rm -v${PWD}:/opt/phpdoc -w /opt/phpdoc phpdoc/dev tools/phive.phar install --force-accept-unsigned

.PHONY: behat
behat:
	docker-compose run --rm behat

.PHONY: phpcs
phpcs:
	docker-compose run --rm phpcs

.PHONY: phpstan
phpstan:
	docker-compose run --rm phpstan

.PHONY: test
test:
	docker-compose run --rm phpunit
	docker-compose run --entrypoint=/usr/local/bin/php --rm phpunit tests/coverage-checker.php 57

.PHONY: pre-commit-test
pre-commit-test: test phpcs phpstan

