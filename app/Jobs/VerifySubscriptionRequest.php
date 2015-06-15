<?php namespace App\Jobs;

use DB;
use Cache;
use App\Jobs\Job;
use GuzzleHttp\Client;
use App\Interfaces\TopicRepositoryInterface;
use App\Interfaces\SubscriberRepositoryInterface;
use App\Interfaces\SubscriptionRepositoryInterface;

class VerifySubscriptionRequest extends Job
{
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
    public function __construct($topicUrl, $callbackUrl, Client $client = null, $challenge = null)
    {
        $this->topicUrl = $topicUrl;
        $this->callbackUrl = $callbackUrl;
        $this->challenge = $challenge ?: bin2hex(openssl_random_pseudo_bytes(16));
        $this->client = $client ?: new Client(['allow_redirects' => false]);
        date_default_timezone_set(env('APP_TIMEZONE'));
    }

    /**
     * The actual Job
     *
     * We shall check the request by making a request to the callback URL
     *
     * @return ...
     */
    public function handle(
        TopicRepositoryInterface $topic,
        SubscriberRepositoryInterface $subscriber,
        SubscriptionRepositoryInterface $subscription
    ) {
        Cache::put($this->challenge, [$this->topicUrl, $this->callbackUrl], 30);

        //Guzzle throws some Exceptions, we don’t want Lumen automatically re-try these
        try {
            $response = $this->client->get($this->callbackUrl, [
                'query' => [
                    'hub.mode' => 'subscribe',
                    'hub.topic' => $this->topicUrl,
                    'hub.challenge' => $this->challenge,
                    'hub.lease_seconds' => env('HUB_LEASE_SECONDS')
                ]
            ]);
            if (substr($response->getStatusCode(), 0, 1) == '2') {
                //now check challenge response
                $returnedChallenge = (string) $response->getBody();
                if (Cache::has($returnedChallenge)) {
                    //add the subscription
                    $topicId = $topic->getIdFromUrl($this->topicUrl);
                    $subscriberId = $subscriber->getIdFromUrl($this->callbackUrl);
                    $subscription->upsert($topicId, $subscriberId);
                    return true;
                } else {
                    $this->delete();
                    return false;
                }
            } else {
                $this->delete();
                return false;
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $this->delete();
            return false;
        }
    }
}
