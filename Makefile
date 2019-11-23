.PHONY:
phar:
	php ./bin/console --env=prod cache:warm; \
	php -d phar.readonly=false tools/box.phar compile --config=box.json

phpcs:
	docker-compose run --rm phpcs

phpstan:
	docker-compose run --rm phpstan

test:
	docker-compose run --rm phpunit

pre-commit-test: test phpcs phpstan
