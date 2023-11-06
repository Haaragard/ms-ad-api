migrate-fresh:
	@docker exec -it ms-ad-api php artisan migrate:fresh

db-seed-client:
	@docker exec -it ms-ad-api php artisan db:seed UserClientSeeder

db-seed-mq:
	@docker exec -it ms-ad-api php artisan db:seed UserSeeder