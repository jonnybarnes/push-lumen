<?php namespace App\Jobs;

use Cache;
use App\Jobs\Job;
use GuzzleHttp\Client;

class SendTopicUpdateNotification extends Job
{
    protected $client;
    protected $callbackUrl;
    protected $topicUrl;
    protected $data;

    public function __construct($callbackUrl, $topicUrl, array $data)
    {
        $this->client = new Client(['defaults' => ['allow_redirects' => false]]);
        $this->callbackUrl = $callbackUrl;
        $this->topicUrl = $topicUrl;
        $this->topicData = $data;
    }

    public function handle()
    {
        $topicData = Cache::get($this->topicUrl);
        $request = $this->client->createRequest('POST', $this->callbackUrl);
        $request->setHeader('Content-Type', $this->topicData['Content-Type']);
        $request->setHeader('Link', env('HUB_URL') . '; rel=hub, ' . $this->topicUrl . '; rel=self');
        //note if guzzle throws an exception, Lumen adds this back
        //to the queue automatically
        $response = $this->client->send($request);
        //but I need to check for re-direct
        if ($response->getStatusCode() == 302) {
            //now throw my own exception to get Lumen to re-add to the queue
            throw new \Exception('Subcription callback URL sent a redirect response');
        }
    }
}
