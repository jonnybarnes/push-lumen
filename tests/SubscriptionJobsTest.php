<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SubscriptionJobsTest extends TestCase
{
    public function testSendNotifications()
    {
        //we need to set up a mock guzzle client
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $mock]);
        $topicUrl = 'http://alice.com/notes';
        $callbackUrl = 'http://bob.com/callback';
        //“mock” a Cache
        Cache::put($topicUrl, ['Content-Type' => 'text/html'], 1);

        $job = new App\Jobs\SendTopicUpdateNotification($topicUrl, $callbackUrl, $client);
        $job->handle();

        return true;
    }
}
