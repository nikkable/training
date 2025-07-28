<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Illuminate\Support\Facades\Log;

class KafkaTestController extends Controller
{
    public function sendMessage(Request $request)
    {
        $topicName = 'my_test_topic';
        $messageBody = [
            'id' => uniqid(),
            'timestamp' => now()->toDateTimeString(),
            'data' => $request->input('message', 'Hello from Laravel Kafka!'),
            'source' => 'laravel-api-producer',
        ];

        // Экземпляр сообщения.
        $message = new Message(
            body: $messageBody,
            key: $messageBody['id'],
            headers: ['content-type' => 'application/json']
        );

        try {
            Kafka::publish()
                ->onTopic($topicName)
                ->withMessage($message)
                ->send();

            Log::info("Message successfully sent to Kafka", ['topic' => $topicName, 'message' => $messageBody]);
            return response()->json(['status' => 'Message sent to Kafka', 'message' => $messageBody]);

        } catch (\Exception $e) {
            Log::error("Error sending message to Kafka: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'Error sending message', 'error' => $e->getMessage()], 500);
        }
    }
}
