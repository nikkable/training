<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\KafkaMessage;

class KafkaConsumerTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumes messages from Kafka test topic.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topicName = 'my_test_topic';
        $this->info("Starting Kafka consumer for topic: {$topicName}");

        Kafka::consumer([$topicName])
            ->withConsumerGroupId('my-test-consumer-group')
            ->withHandler(function(KafkaMessage $message) {

                $this->info("Received message:");
                $this->info("Topic: " . $message->getTopicName());
                $this->info("Partition: " . $message->getPartition());
                $this->info("Key: " . $message->getKey());
                $this->info("Headers: " . json_encode($message->getHeaders()));

                $body = $message->getBody();
                $this->info("Body: " . json_encode($body));

                if (is_array($body) && isset($body['data'])) {
                    $this->info("Data from message: " . $body['data']);
                }

                $this->info("Message processed successfully.");

            })
            ->build()
            ->consume();

        $this->info("Kafka consumer stopped.");

        return Command::SUCCESS;
    }
}
