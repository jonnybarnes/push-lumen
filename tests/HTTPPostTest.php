<?php

class HTTPPostTest extends TestCase
{
    public function test400ReponseWhenNoModeSet()
    {
        $this->call('POST', '/');

        $this->assertResponseStatus(400);
    }

    public function test501ResponseFromUnkownMode()
    {
        $this->call('POST', '/', ['hub_mode' => 'unkown']);

        $this->assertResponseStatus(501);
    }

    public function testQueueFromSuccessfulPublish()
    {
        Queue::shouldReceive('push')->once();

        $this->call('POST', '/', ['hub_mode' => 'publish', 'hub_url' => 'https://example.org/']);

        $this->assertResponseStatus(202);
    }

    public function testQueueFromSuccessfulSubscribe()
    {
        Queue::shouldReceive('push')->once();

        $this->call('POST', '/', [
            'hub_mode' => 'subscribe',
            'hub_topic' => 'https://example.org/',
            'hub_callback' => 'https://mysite.com/callback'
        ]);

        $this->assertResponseStatus(202);
    }

    public function testQueueFromSuccessfulUnsubscribe()
    {
        Queue::shouldReceive('push')->once();

        $this->call('POST', '/', [
            'hub_mode' => 'unsubscribe',
            'hub_topic' => 'https://example.org/',
            'hub_callback' => 'https://mysite.com/callback'
        ]);

        $this->assertResponseStatus(202);
    }
}
