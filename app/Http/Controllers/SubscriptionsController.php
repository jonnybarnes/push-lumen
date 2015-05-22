<?php namespace App\Http\Controllers;

use Queue;
use League\Url\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Jobs\VerifySubscriptionRequest;

class SubscriptionsController extends Controller
{
    /**
     * The topic URL
     *
     * @var \League\Url\Url
     */
    protected $topicUrl;

    /**
     * The subscriber’s callback URL
     *
     * @var \League\Url\Url
     */
    protected $callbackUrl;

    /**
     * Assing the values in our construct
     *
     * @param  string
     * @param  string
     * @return void
     */
    public function __construct($topicUrl = null, $callbackUrl = null)
    {
        $this->topicUrl = $topicUrl;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * Initiate a subscription
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function subscribe(Request $request)
    {
        //we've already checked hub.mode = subscribe in HTTPPostController
        if (null !== $request->input('hub_topic') && null !== $request->input('hub_callback')) {
            //Guzzle fails on schemeless URLs
            $this->topicUrl = Url::createFromUrl($request->input('hub_topic'));
            if (substr($this->topicUrl, 0, 2) == '//') {
                $this->topicUrl->setScheme('http');
            }
            $this->callbackUrl = Url::createFromUrl($request->input('hub_callback'));
            if (substr($this->callbackUrl, 0, 2) == '//') {
                $this->callbackUrl->setScheme('http');
            }
            Queue::push(new VerifySubscriptionRequest((string) $this->topicUrl, (string) $this->callbackUrl));
        }
    }

    /**
     * Initiaite an unsubscription request
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function unsubscribe(Request $request)
    {
        //we’ve already checked hub.mode = unsubscribe in HTTPPostController
        if (null !== $request->input('hub_topic') && null !== $request->input('hub_callback')) {
            //Guzzle fails with schemeless URLs
            $this->topicUrl = Url::createFromUrl($request->input('hub_topic'));
            if (substr($this->topicUrl, 0, 2) == '//') {
                $this->topicUrl->setScheme('http');
            }
            $this->callbackUrl = Url::createFromUrl($request->input('hub_callback'));
            if (substr($this->callbackUrl, 0, 2) == '//') {
                $this->callbackUrl->setScheme('http');
            }
            Queue::push(new VerifyUnsubscriptionRequest((string) $this->topicUrl, (string) $this->callbackUrl));
        }
    }
}
