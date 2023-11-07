migrate-fresh:
	@docker exec -it ms-ad-api php artisan migrate:fresh

.PHONY: benchmark
benchmark:
	@docker exec -it ms-ad-api php artisan benchmark-user-create
