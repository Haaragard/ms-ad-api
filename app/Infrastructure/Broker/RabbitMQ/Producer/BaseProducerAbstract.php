<?php declare(strict_types=1);

namespace App\Infrastructure\Broker\RabbitMQ\Producer;

use App\Infrastructure\Broker\RabbitMQ\RabbitMQBroker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

abstract class BaseProducerAbstract
{
    private RabbitMQBroker $broker;
    private AMQPChannel $channel;

    // -- Overridable attributes --

    // Exchange
    protected string $exchangePrefix = 'ms_ad';
    protected string $exchangeName = 'default';
    protected string $exchangeType = 'topic';
    protected string $routingKey = '';
    protected bool $mandatory = false;
    protected bool $immediate = false;
    protected int|null $ticket = null;
    protected array $messageProperties = [];

    public function __construct()
    {
        $this->declareBroker();
        $this->startChannel();
        $this->configChannel();
    }

    protected function getExchangeName(): string
    {
        return sprintf('%s.%s', $this->exchangePrefix, $this->exchangeName);
    }

    private function declareBroker(): void
    {
        $this->broker = new RabbitMQBroker(
            config('queue.connections.rabbitmq.host'),
            (int) config('queue.connections.rabbitmq.port'),
            config('queue.connections.rabbitmq.user'),
            config('queue.connections.rabbitmq.password'),
            config('queue.connections.rabbitmq.vhost')
        );
    }

    private function startChannel(): void
    {
        $this->channel = $this->broker->getChannel();
    }

    private function configChannel(): void
    {
        $this->channel->exchange_declare(
            $this->getExchangeName(),
            $this->exchangeType,
            false,
            true,
            false
        );
    }

    public function basicPush(): void
    {
        $this->channel->basic_publish(
            $this->getBasicMessage(),
            $this->getExchangeName(),
            $this->routingKey,
            $this->mandatory,
            $this->immediate,
            $this->ticket
        );
    }

    private function getBasicMessage(): AMQPMessage
    {
        $messageContent = json_encode($this->messageContent());
        
        return new AMQPMessage($messageContent, $this->messageProperties);
    }

    abstract protected function messageContent(): array;
}
