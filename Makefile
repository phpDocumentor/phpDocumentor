.PHONY:
phar:
	php ./bin/console --env=prod cache:warm; \
	php -d phar.readonly=false tools/box.phar compile --config=box.json
