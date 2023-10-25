# Microservice AD API

This project was made entirely focused on Microservices + RabbitMQ study.
+ We have the usage of Publishes on RabbitMQ QUEUES

## Start up

### Requirements

- Docker
- Docker Compose

### Step by step

1. Prepare envs
    ```bash
    cp .env.example .env
    ```
2. Build
    ```bash
    docker compose build
    ```
3. Start
    ```bash
    docker compose up -d
    ```

**Necessário criar chave, migrate no banco & composer_install**

## Packages

- [Laravel](https://laravel.com)
- [RabbitMQ](https://www.rabbitmq.com/)
- [RabbitMQ AMQP PHP](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)
