<?php declare(strict_types=1);

namespace App\Infrastructure\Broker\RabbitMQ\Producer;

use App\Enums\BrokerEnum;
use App\Models\User;

class UserCreatedProducer extends BaseProducerAbstract
{
    protected string $routingKey = BrokerEnum::USER_CREATED_EVENT->value;

    public function __construct(private User $user)
    {
        parent::__construct();
    }

    protected function messageContent(): array
    {
        return $this->user->toArray();
    }
}