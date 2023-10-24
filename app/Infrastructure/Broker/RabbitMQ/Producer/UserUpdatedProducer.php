<?php declare(strict_types=1);

namespace App\Infrastructure\Broker\RabbitMQ\Producer;

use App\Enums\BrokerEnum;
use App\Models\User;

class UserUpdatedProducer extends BaseProducerAbstract
{
    protected string $routingKey = BrokerEnum::USER_UPDATED_EVENT->value;

    public function __construct(private User $user)
    {
        parent::__construct();
    }

    protected function messageContent(): array
    {
        return $this->user->toArray();
    }
}