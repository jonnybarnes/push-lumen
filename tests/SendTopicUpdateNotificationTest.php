<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SendTopicUpdateNotificationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->callbackUrl = 'https://bob.com/callback';
        $this->topicUrl = 'https://alice.com/notes';
    }

    public function testSuccessfulNotification()
    {
        $mock = new MockHandler([
            new Response(202)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $job = new App\Jobs\SendTopicUpdateNotification($this->callbackUrl, $this->topicUrl, $client);
        $this->assertTrue($job->handle());
    }

    /**
     * @expectedException \GuzzleHttp\Exception\BadResponseException
     */
    public function testBadResponseNotification()
    {
        $mock = new MockHandler([
            new Response(500)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $job = new App\Jobs\SendTopicUpdateNotification($this->callbackUrl, $this->topicUrl, $client);
        $job->handle();
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Subcription callback URL sent a redirect response
     */
    public function testRedirectResponseNotification()
    {
        $mock = new MockHandler([
            new Response(302)
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $job = new App\Jobs\SendTopicUpdateNotification($this->callbackUrl, $this->topicUrl, $client);
        $job->handle();
    }
}
