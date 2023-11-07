<?php

namespace App\Console\Commands;

use App\Infrastructure\Broker\RabbitMQ\Producer\UserCreatedProducer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BenchmarkUserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark-user-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Benchmark of user creation with communication with ms-consumer-api';
    
    public function __construct(private UserCreatedProducer $producer)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $measures = Benchmark::measure([
            'syncCreation' => fn() => $this->syncCreation(),
            'asyncCreation' => fn() => $this->asyncCreation(),
        ], 100);

        $result = collect($measures)
            ->map(fn ($average) => number_format($average, 3).'ms');

        $this->table($result->keys()->toArray(), [$result->values()->toArray()]);

        return Command::SUCCESS;
    }
    
    private function syncCreation(): void
    {
        $user = User::factory()->create();

        DB::transaction(function () use ($user) {
            $user->save();

            $response = Http::post('http://ms-consumer-api/api/users', $user->toArray());
            if ($response->failed()) {
                Log::error("Error on client post {$user->id}", $response->json());
            }
        });
    }

    private function asyncCreation(): void
    {
        $user = User::factory()->create();

        DB::transaction(function () use ($user) {
            $user->save();
            $this->producer->setUser($user);
            $this->producer->basicPush();
        });
    }
}
