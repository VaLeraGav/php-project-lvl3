start:
	php artisan serve
	# http://127.0.0.1:8000/

start-deploy:
    php -S 0.0.0.0:${PORT:-8000} -t public

setup:
	composer install
	cp -n .env.example .env
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate
	php artisan db:seed
	npm ci
	npm run build
	make ide-helper

test:
	php artisan test

test-coverage:
	XDEBUG_MODE=coverage php artisan test --coverage-clover build/logs/clover.xml

migrate:
	php artisan migrate

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

deploy:
	git push heroku

#компиляции ресурсов
watch:
	npm run watch

dev:
	npm run dev