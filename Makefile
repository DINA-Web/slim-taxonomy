#!make
# documentation here, https://github.com/DINA-Web/slim-taxonomy

include .env

all: init up

.PHONY: all

init:
	@echo "Running the docker ${IMAGE}:${TAG}"
	@test -f env/.env-mysql || (cp env/template.env-mysql env/.env-mysql && echo "OBS : created the file env/.env-mysql - please fill in the credentials")
	@sleep 2;

build:
	@docker build -t ${IMAGE}:${TAG} .

up: build
	@docker-compose up -d

up-dev:
	@docker-compose -f docker-compose.yml.local up -d

stop:
	@docker-compose stop

down:
	@docker-compose down

release: #docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
	@docker push ${IMAGE}:${TAG}

test-localhost_domain:
	xdg-open http://localhost:90/taxon/13001562 &

test-alpha_domain:
	xdg-open https://alpha-slimtaxonomy.dina-web.net/taxon/13001562 &

logs:
	@docker-compose logs -f --tail=20
