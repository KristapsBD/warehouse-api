up:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

recreate:
	./vendor/bin/sail down
	./vendor/bin/sail up -d --build --force-recreate

migrate:
	./vendor/bin/sail artisan migrate

migrate-fresh:
	./vendor/bin/sail artisan migrate:fresh --seed

shell:
	./vendor/bin/sail shell

db:
	./vendor/bin/sail mysql

install:
	./vendor/bin/sail composer install

test:
	./vendor/bin/sail test

logs:
	./vendor/bin/sail logs -f

lint:
	./vendor/bin/sail bin pint

setup:
	mkdir -p vendor
	docker run --rm \
		-u "$(shell id -u):$(shell id -g)" \
		-v "$(shell pwd):/var/www/html" \
		-w /var/www/html \
		laravelsail/php84-composer:latest \
		composer install --ignore-platform-reqs
	$(MAKE) up
