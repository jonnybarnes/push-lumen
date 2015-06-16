<?php namespace App\Http\Controllers;

use Queue;
use App\Jobs\CheckTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    /**
     * Assing the values in our construct
     *
     * @param  string
     * @return void
     */
    public function __construct($topicUrl = null)
    {
        $this->topicUrl = $topicUrl;
    }
    
    /**
     * Here we simply queue the job of checking the topic url for updates
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function update(Request $request)
    {
        //hub.mode has already been checked to be `publish`
        if (null !== $request->input('hub_url')) {
            Queue::push(new CheckTopic($request->input('hub_url')));
        }
    }
}
