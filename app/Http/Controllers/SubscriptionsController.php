<?php namespace App\Http\Controllers;

use Queue;
use League\Url\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Jobs\VerifySubscriptionRequest;
use App\Jobs\VerifyUnsubscriptionRequest;

class SubscriptionsController extends Controller
{
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
            Queue::push(new VerifySubscriptionRequest($request->input('hub_topic'), $request->input('hub_callback')));
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
            Queue::push(new VerifyUnsubscriptionRequest($request->input('hub_topic'), $request->input('hub_callback')));
        }
    }
}
