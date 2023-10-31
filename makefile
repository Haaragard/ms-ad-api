migrate-fresh:
	@docker exec -t ms-ad-api php artisan migrate:fresh

db-seed-client:
	@docker exec -t ms-ad-api php artisan db:seed UserClientSeeder

db-seed-mq:
	@docker exec -t ms-ad-api php artisan db:seed UserSeeder