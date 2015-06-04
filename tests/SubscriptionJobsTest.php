<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Tests\Server;

class SubscriptionJobsTest extends TestCase
{
    public function setUp()
    {
        $this->topicUrl = 'http://alice.com/notes';
        $this->callbackUrl = 'http://bob.com/callback';
        Cache::put($this->topicUrl, ['Content-Type' => 'text/html'], 5);
    }

    public function testSendNotificationsSuccess()
    {
        //we need to set up a mock guzzle client
        $mock = new MockHandler([
            new Response(202)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $job = new App\Jobs\SendTopicUpdateNotification($this->topicUrl, $this->callbackUrl, $client);
        $job->handle();
    }

    /**
     * @expectedException GuzzleHttp\Exception\ServerException
     */
    public function testSendNotificationsFailure()
    {
        $mock = new MockHandler([
            new Response(500, ['Content-Length' => 0])
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $job = new App\Jobs\SendTopicUpdateNotification($this->topicUrl, $this->callbackUrl, $client);
        $job->handle();
    }
}
