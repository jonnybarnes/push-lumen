<?php namespace App\Jobs;

use DB;
use Cache;
use App\Jobs\Job;
use GuzzleHttp\Client;

class VerifySubscriptionRequest extends Job
{
    use \Illuminate\Queue\InteractsWithQueue;

    /**
     * The topic URL
     *
     * @var string
     */
    protected $topicUrl;

    /**
     * The subscriber’s callback URL
     *
     * @var string
     */
    protected $callbackUrl;

    /**
     * The challenge parameter
     *
     * @var string
     */
    protected $challenge;

    /**
     * The HTTP client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Let’s set up the job
     *
     * @param string  The topic URL
     * @param string  The callback URL
     * @param DB
     * @return void
     */
    public function __construct($topicUrl, $callbackUrl, $challenge = null)
    {
        $this->topicUrl = $topicUrl;
        $this->callbackUrl = $callbackUrl;
        $this->challenge = $challenge;
        $this->client = new Client(['defaults' => ['allow_redirects' => false]]);
        date_default_timezone_set(env('APP_TIMEZONE'));
    }

    /**
     * The actual Job
     *
     * We shall check the request by making a request to the callback URL
     *
     * @return ...
     */
    public function handle(TopicRepositoryInterface $topic, SubscriberRepositoryInterface $subscriber, SubscriptionRepositoryInterface $subscription)
    {
        $this->challenge = bin2hex(openssl_random_pseudo_bytes(16));
        //now we cache the challenge value to check for when returned
        Cache::put($this->challenge, [$this->topicUrl, $this->callbackUrl], 30);
        $request = $this->client->createRequest('GET', $this->callbackUrl);
        $query = $request->getQuery();
        $query['hub.mode'] = 'subscribe';
        $query['hub.topic'] = $this->topicUrl;
        $query['hub.challenge'] = $this->challenge;
        $query['hub.lease_seconds'] = env('HUB_LEASE_SECONDS');
        //Guzzle throws some Exceptions, we don’t want Laravel automatically re-tring these
        try {
            $response = $this->client->send($request);
            if (substr($response->getStatusCode(), 0, 1) == '2') {
                //now check challenge response
                $returnedChallenge = (string) $response->getBody();
                if (Cache::has($returnedChallenge)) {
                    //add the subscription
                    $topic_id = $topic->getIdFromUrl($this->topicUrl);
                    $subscriber_id = $subscriber->getIdFromUrl($this->callbackUrl);
                    $subscription->upsert($topic_id, $subscriber_id);
                } else {
                    $this->delete();
                }
            } else {
                $this->delete();
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $this->delete();
        }
    }
}
