<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class CheckTopicTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->url = 'https://alice.com/notes';
        Cache::put($this->url, ['Content-Type' => 'text/html', 'hash' => 1], 5);
    }

    public function testCheckTopicNull()
    {
        //we need to set up a mock guzzle client
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        //and now a TopicRepositoryInterface mock
        $topicMock = Mockery::mock('App\Interfaces\TopicRepositoryInterface');
        $topicMock->shouldReceive('getSubscribers')->andReturn(null);

        $job = new App\Jobs\CheckTopic($this->url, $client);
        $this->assertEquals(0, $job->handle($topicMock));
    }

    public function testCheckTopic()
    {
        //let’s not actually use the Queue...
        Queue::shouldReceive('push')->twice();

        //we need to set up a mock guzzle client
        $mock = new MockHandler([
            new Response(200)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        //and now a TopicRepositoryInterface mock, with some data to return
        $sub1 = new stdClass();
        $sub1->url = 'http://url1.example.org';
        $sub2 = new stdClass();
        $sub2->url = 'http://url1.example.org';
        $topicMock = Mockery::mock('App\Interfaces\TopicRepositoryInterface');
        $topicMock->shouldReceive('getSubscribers')->andReturn([
            1 => $sub1,
            2 => $sub2
        ]);

        $job = new App\Jobs\CheckTopic($this->url, $client);
        $this->assertEquals(1, $job->handle($topicMock));
    }
}
