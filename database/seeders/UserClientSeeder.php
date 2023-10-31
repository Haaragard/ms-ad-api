<?php

namespace Database\Seeders;

use App\Infrastructure\Broker\RabbitMQ\Producer\UserCreatedProducer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Log;

class UserClientSeeder extends Seeder
{
    public function __construct(private UserCreatedProducer $producer)
    { }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(1000)->make();

        $start = microtime(true);
        foreach ($users as $user) {
            $this->storeUser($user);
        }
        $end = microtime(true);

        $diff = $end - $start;
        $diffInMs = round($diff * 1000,2);
        echo "". $diffInMs ." ms";
    }

    private function storeUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->save();

            $response = Http::post('http://ms-consumer-api/api/users', $user->toArray());
            if ($response->failed()) {
                Log::error("Error on client post {$user->id}", $response->json());
            }
        });
    }
}
