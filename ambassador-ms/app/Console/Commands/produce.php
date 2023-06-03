<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class produce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'produce';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', 'pkc-4r297.europe-west1.gcp.confluent.cloud:9092');
        $conf->set('security.protocol', 'SASL_SSL');
        $conf->set('sasl.mechanisms', 'PLAIN');
        $conf->set('sasl.username', 'UENDWJQDN7B7DBMI');
        $conf->set('sasl.password', 'is87aarLTthoIGc0dmaMlm26vgyw7USltr/1pGqyIBrTaT8kpZw6kusZce3AN7fD');
        $producer = new \RdKafka\Producer($conf);

        $topic = $producer->newTopic("email_queue");

        for ($i = 0; $i < 10; $i++) {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, serialize('{message:"hahaha"}'));
            $producer->poll(0);
        }

        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }

    }
}
