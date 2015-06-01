<?php

class SubscriptionJobsTest extends TestCase
{
    public function testSendNotifications()
    {
        //we need to set up a mock guzzle client
        $topicUrl = 'http://alice.com/notes';
        $callbackUrl = 'http://bob.com/callback';
        Cache::shouldReceive('get')
                ->once()
                ->andReturn([
                    'Content-Type' => 'text/html'
                ]);

        $job = new App\Jobs\SendTopicUpdateNotification($topicUrl, $callbackUrl);
    }
}
