<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

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

    public function testVerifySubscriptionRequest()
    {
        $guzzleMock = new MockHandler([
            new Response(200, [], 'test')
        ]);
        $handler = HandlerStack::create($guzzleMock);
        $client = new Client(['handler' => $handler]);

        $topicMock = Mockery::mock('App\Interfaces\TopicRepositoryInterface');
        $topicMock->shouldReceive('getIdFromUrl')->andReturn(1);

        $subscriberMock = Mockery::mock('App\Interfaces\SubscriberRepositoryInterface');
        $subscriberMock->shouldReceive('getIdFromUrl')->andReturn(2);

        $subscriptionMock = Mockery::mock('App\Interfaces\SubscriptionRepositoryInterface');
        $subscriptionMock->shouldReceive('upsert')->andReturn(true);

        $job = new App\Jobs\VerifySubscriptionRequest($this->topicUrl, $this->callbackUrl, $client, 'test');
        $job->handle($topicMock, $subscriberMock, $subscriptionMock);
    }
}
