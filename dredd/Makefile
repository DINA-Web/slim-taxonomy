#!make


all:
.PHONY: all

build:
	docker-compose up --build

up:
	# Note uppercase PWD!
	docker run -ti -v $(PWD)/../apiary.apib:/home/apiary.apib --net="host" mikkohei13/dredd:0.1
