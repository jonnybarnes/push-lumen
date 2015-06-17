<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SubscriptionJobsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
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

    public function testVerifySubscriptionRequestSuccess()
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
        $this->assertTrue($job->handle($topicMock, $subscriberMock, $subscriptionMock));
    }

    public function testVerifySubscriptionRequestFailureStatusCode()
    {
        $guzzleMock = new MockHandler([
            new Response(500, [])
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
        $this->assertFalse($job->handle($topicMock, $subscriberMock, $subscriptionMock));
    }

    public function testVerifySubscriptionRequestFailureWithCache()
    {
        $guzzleMock = new MockHandler([
            new Response(200, [], 'not-test')
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
        $this->assertFalse($job->handle($topicMock, $subscriberMock, $subscriptionMock));
    }

    public function testVerifyUnsubscriptionRequestSuccess()
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
        $subscriptionMock->shouldReceive('delete')->andReturn(true);

        $job = new App\Jobs\VerifyUnsubscriptionRequest($this->topicUrl, $this->callbackUrl, $client, 'test');
        $this->assertTrue($job->handle($topicMock, $subscriberMock, $subscriptionMock));
    }
}
