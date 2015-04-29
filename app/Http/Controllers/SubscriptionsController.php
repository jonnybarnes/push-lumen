<?php namespace App\Http\Controllers;

use Queue;
use League\Url\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

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
     * Initiate a subscription
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe($request)
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
            Queue::push(new VerifySubscriptionRequest($this->topicUrl, $this->callbackUrl));
            return (new Response('Subscription request received', 202));
        } else {
            return (new Response('You are missing some parameters.', 400));
        }
    }
}
