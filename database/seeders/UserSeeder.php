<?php

namespace Database\Seeders;

use App\Infrastructure\Broker\RabbitMQ\Producer\UserCreatedProducer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function __construct(private UserCreatedProducer $producer)
    { }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(100)->make();

        foreach ($users as $user) {
            $this->storeUser($user);
        }
    }

    private function storeUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->save();
            $this->producer->setUser($user);
            $this->producer->basicPush();
        });
    }
}
