app-up: app-backend app-frontend

app-backend:
	cd backend
	docker compose -f ./backend/docker-compose.yml up --build -d
	docker exec backend composer migrate-and-seed

app-frontend:
	yarn --cwd ./frontend
	yarn --cwd ./frontend serve

app-csfixer:
	docker exec backend vendor/bin/php-cs-fixer fix --config=./.php-cs-fixer.php -v
