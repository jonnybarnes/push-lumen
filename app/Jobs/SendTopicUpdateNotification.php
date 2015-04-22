<?php namespace App\Jobs;

use Cache;
use App\Jobs\Job;
use GuzzleHttp\Client;

class SendTopicUpdateNotification extends Job
{
    /**
     * The Guzzle client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;
    
    /**
     * The subscriber’s callback URL
     *
     * @var string
     */
    protected $callbackUrl;
    
    /**
     * The topic’s URL
     *
     * @var string
     */
    protected $topicUrl;
    
    /**
     * The Cached data for the topic (Content-Type + hash)
     *
     * @var array
     */
    protected $data;

    /**
     * Construct the controller, we don’t want to allow redirects in Guzzle either
     *
     * @param string  The subscriber’s callback URL
     * @param string  The topic URL
     * @return void
     */
    public function __construct($callbackUrl, $topicUrl)
    {
        $this->client = new Client(['defaults' => ['allow_redirects' => false]]);
        $this->callbackUrl = $callbackUrl;
        $this->topicUrl = $topicUrl;
        $this->topicData = Cache::get($this->topicUrl);
    }

    /**
     * Do the job! That is, make a POST request to the subscriber’s URL with
     * the notification payload.
     *
     * @return void
     */
    public function handle()
    {
        $request = $this->client->createRequest('POST', $this->callbackUrl);
        $request->setHeader('Content-Type', $this->topicData['Content-Type']);
        $request->setHeader('Link', env('HUB_URL') .'; rel=hub, ' . $this->topicUrl . '; rel=self');
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
