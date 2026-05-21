.PHONY: init
init: src/.env src/.env.testing
	docker compose up -d --build
	docker compose run --rm laravel composer install
	docker compose run --rm laravel php artisan key:generate
	docker compose run --rm laravel php artisan migrate

	# テスト用
	docker compose run --rm laravel php artisan key:generate --env=testing
	docker compose run --rm laravel php artisan migrate --env=testing

	docker compose up -d

src/.env:
	@if [ ! -f ./src/.env ]; then \
		cp ./src/.env.example ./src/.env; \
	fi

src/.env.testing:
	@if [ ! -f ./src/.env.testing ]; then \
		cp ./src/.env.example ./src/.env.testing; \
	fi
