<?php namespace App\Jobs;

use DB;
use Cache;
use Queue;
use App\Jobs\Job;
use GuzzleHttp\Client;
use App\Jobs\SendTopicUpdateNotification;
use App\Interfaces\TopicRepositoryInterface;

class CheckTopic extends Job
{
    /**
     * The URL of the topic
     *
     * @var string
     */
    protected $url;

    /**
     * The Guzzle client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Set up the Job
     *
     * @param string  Thus URL of the topic
     * @return void
     */
    public function __construct($url, Client $client = null)
    {
        $this->url = $url;
        $this->client = $client ?: new Client();
    }

    /**
     * Check topic URL for updates, if that’s the case, update any subscribers
     *
     * @return void
     */
    public function handle(TopicRepositoryInterface $topic)
    {
        $notify = false;
        $response = $this->client->get($this->url);
        $contentType = $response->getHeader('Content-Type');
        $hash = md5((string) $response->getBody());
        if (Cache::has($this->url)) {
            $data = Cache::get($this->url);
            if ($data['Content-Type'] != $contentType) {
                $data['Content-Type'] = $contentType;
                $notify = true;
            }
            if ($data['hash'] != $hash) {
                $data['hash'] = $hash;
                $notify = true;
            }
            //something has changed, so re-save to the cache, and ping subscribers
            if ($notify) {
                Cache::forever($this->url, $data);
            }
        } else {
            //nothing in the cache, so first time accessing this topic
            $notify = true;
            $data = [
                'Content-Type' => $contentType,
                'hash' => $hash
            ];
            Cache::forever($this->url, $data);
        }
        if ($notify) {
            $subs = $topic->getSubscribers($this->url);
            if (null !== $subs) {
                foreach ($subs as $sub) {
                    Queue::push(new SendTopicUpdateNotification($sub->url, $this->url));
                }
                return 1;
            }
            return 0;
        }
        return 0;
    }
}
