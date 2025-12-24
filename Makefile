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
	./vendor/bin/sail artisan migrate:fresh

seed:
	./vendor/bin/sail artisan db:seed

shell:
	./vendor/bin/sail shell

install:
	./vendor/bin/sail composer install

test:
	./vendor/bin/sail test
