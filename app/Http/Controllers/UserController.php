<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Infrastructure\Broker\RabbitMQ\Producer\UserCreatedProducer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = $data['password'] ?? '123456';

        $user = new User($data);
        $producer = new UserCreatedProducer($user);

        DB::transaction(function () use ($user, $producer) {
            $user->save();
            $producer->basicPush();
        });

        $response = response()->json([
            'message' => 'User created',
            'data' => $user->toArray(),
        ], Response::HTTP_CREATED);

        return $response;
    }
}