<?php
declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service;

use App\Constants\ErrorCode;
use App\Contract\ChatServiceInterface;
use OpenAI;

class ChatServiceImplement implements ChatServiceInterface
{
    public function getInitContext(): array
    {
        return [
            ["role" => "system", "content" => "You are ChatGPT, a Large Language model trained by OpenAI. Follow the User's instructions carefully"],
            ["role" => "user", "content" => "You are ChatGPT,"],
        ];
    }

    public function chat(array $context, float $temperature = 1): array
    {
        $key = config('open_ai.api_key');
        if (!$key) {
            abort(ErrorCode::SERVER_ERROR);
        }
        $client = OpenAI::client($key);
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $context,
            'temperature' => $temperature
        ]);
        $response = $result->choices[0]->message->content;
        $context[] = ["role" => "assistant", "content" => $response];
        return $context;
    }
}