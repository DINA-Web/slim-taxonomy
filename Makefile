#!make
PWD=$(shell pwd)
DOCKER_IMAGE=dina/slim-taxonomy
DOCKERHUB_VER=0.2

all: init up

.PHONY: all

init:
	@echo "Running version ${DOCKER_IMAGE}:${DOCKERHUB_VER}"
	sleep 2;

build:
	@docker build -t ${DOCKER_IMAGE}:${DOCKERHUB_VER} .

up:
	@docker-compose up -d

up-dev:
	@docker-compose -f docker-compose.yml.local up -d

stop:
	@docker-compose stop

down:
	@docker-compose down

release: #docker login
	@docker push -t ${DOCKER_IMAGE}:${DOCKERHUB_VER}

test-locally:
	xdg-open http://localhost:90/taxon/13001562

logs:
	@docker-compose logs -f --tail=20
