<?php namespace App\Jobs;

use Cache;
use App\Jobs\Job;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

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
    protected $topicData;

    /**
     * Construct the controller, we don’t want to allow redirects in Guzzle either
     *
     * @param string  The subscriber’s callback URL
     * @param string  The topic URL
     * @param GuzzleHttp\Client
     * @return void
     */
    public function __construct($callbackUrl, $topicUrl, Client $client = null)
    {
        $this->client = $client ?: new Client(['allow_redirects' => false]);
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
        //set the request headers
        $headers = [
            'Content-Type' => $this->topicData['Content-Type'],
            'Link' => env('HUB_URL') .'; rel=hub, ' . $this->topicUrl . '; rel=self'
        ];
        //create the request
        $request = new Request('POST', $this->callabkUrl, $headers);

        //note if guzzle throws an exception, Lumen adds this back
        //to the queue automatically
        $response = $this->client->send($request);
        //but I need to check for re-direct
        if (substr($response->getStatusCode(), 0, 1) == 3) {
            //now throw my own exception to get Lumen to re-add to the queue
            throw new \Exception('Subcription callback URL sent a redirect response');
        }
    }
}
