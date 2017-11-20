#!make
PWD=$(shell pwd)
include .env

all: init up

.PHONY: all

init:
	@echo "Running the docker ${IMAGE}:${TAG}"
	sleep 2;

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

release: #docker login
	@docker push -t ${IMAGE}:${TAG}

browser-locally:
	xdg-open http://localhost:90/taxon/13001562

browser-test:
	xdg-open https://alpha-slimtaxonomy.dina-web.net/taxon/13001562

logs:
	@docker-compose logs -f --tail=20
