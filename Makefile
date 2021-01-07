.PHONY: install tests

all: install

install:
	composer install

tests:
	bin/phpunit
	bin/var-dump-check --symfony --exclude vendor src
