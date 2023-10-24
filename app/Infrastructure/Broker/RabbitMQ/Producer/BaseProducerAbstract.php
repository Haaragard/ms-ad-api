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
    protected string $queue = 'default';
    protected string $exchange = '';
    protected string $routingKey = '';
    protected bool $mandatory = false;
    protected bool $immediate = false;
    protected int|null $ticket = null;
    protected array $messageProperties = [];

    public function __construct()
    {
        $this->declareBroker();
        $this->startChannel();
        $this->declareQueue();
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

    private function declareQueue(): void
    {
        $this->channel->queue_declare($this->queue);
    }

    public function basicPush(): void
    {
        $this->channel->basic_publish(
            $this->getBasicMessage(),
            $this->exchange,
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
