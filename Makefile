DOCKER_ENV_FILE_PATH = docker/.env
DOCKER_ENV_LOCAL_FILE_PATH = docker/.env.local

ifeq ($(shell test -f ${DOCKER_ENV_LOCAL_FILE_PATH} && echo yes),yes)
    DOCKER_ENV_FILE_PATH = ${DOCKER_ENV_LOCAL_FILE_PATH}
endif

include ${DOCKER_ENV_FILE_PATH}

DOCKER_COMPOSE = docker compose --env-file ${DOCKER_ENV_FILE_PATH}
DOCKER_COMPOSE_PHP_EXEC = ${DOCKER_COMPOSE} exec php

##################
## Docker
##################

dc_build: #build services
	${DOCKER_COMPOSE} build

dc_up: #up containers
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_network:
	docker network create -d bridge cw-smartlinks-userauth-network

dc_rebuild_and_up: #stop, build services again and up them
	${DOCKER_COMPOSE} down --remove-orphans
	docker network rm cw-smartlinks-userauth-network
	${DOCKER_COMPOSE} build
	docker network create -d bridge cw-smartlinks-userauth-network
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_ps: #show containers list
	${DOCKER_COMPOSE} ps -a

dc_down: #down containers
	${DOCKER_COMPOSE} down --remove-orphans

dc_enter_php: #enter php container
	${DOCKER_COMPOSE} exec php bash

dc_logs_php: #show php container logs
	${DOCKER_COMPOSE} logs php

dc_enter_nginx: #enter nginx container
	${DOCKER_COMPOSE} exec nginx bash

dc_logs_nginx: #show nginx container logs
	${DOCKER_COMPOSE} logs nginx

##################
## Migrations
##################

db_diff: #generate new database migration
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:migrations:diff --no-interaction

db_migrate: #execute database migrations
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:migrations:migrate --no-interaction


##################
## CS-Fixer
##################

cs_check:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/php-cs-fixer fix --dry-run

cs_fix:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/php-cs-fixer fix


##################
## Cache
##################

cache_clear:
	${DOCKER_COMPOSE_PHP_EXEC} rm -Rf var/cache/*
	${DOCKER_COMPOSE_PHP_EXEC} bin/console cache:clear
	${DOCKER_COMPOSE_PHP_EXEC} bin/console cache:clear --env=test


##################
## Install dependecies via composer
##################

composer_install:
	${DOCKER_COMPOSE_PHP_EXEC} composer install -n


##################
## Analyze layers structure
##################

deptrac:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/deptrac analyse

##################
## Analyze YAML files
##################

yamllint:
	yamllint .


##################
## PHPStan analysis
##################

phpstan:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpstan analyse src tests --memory-limit=1G


##################
## Tests
##################

tests_all:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests

tests_coverage_xml:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests --coverage-clover=coverage/clover.xml

tests_coverage_html:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests --coverage-html coverage/

tests_coverage_text_summary:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests --coverage-text  --only-summary-for-coverage-text

tests_init:
	${DOCKER_COMPOSE_PHP_EXEC} composer install -n
	${DOCKER_COMPOSE_PHP_EXEC} bin/console cache:clear --env=test
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:database:drop --if-exists --env=test --force -n
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:database:create --env=test -n
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:migrations:migrate --env=test --quiet -n

tests_unit:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests/Unit

tests_integration:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests/Integration

tests_functional:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/phpunit tests/Functional


##################
## JWT keys
##################

jwt:
	${DOCKER_COMPOSE_PHP_EXEC}  bin/console lexik:jwt:generate-keypair