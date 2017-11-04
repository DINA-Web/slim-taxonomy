#!make


all:
.PHONY: all

build:
	docker-compose up --build

up:
	# Note uppercase PWD!
	docker run -ti \
		-v $(PWD)/../apiary.apib:/home/apiary.apib \
		--net="host" \
		--name slim_taxonomy_dredd \
		mikkohei13/dredd:0.1

clean:
	docker stop slim_taxonomy_dredd
	docker rm slim_taxonomy_dredd