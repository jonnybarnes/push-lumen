<?php namespace App\Http\Controllers;

use DB;
use League\Url\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    /**
     * The topic URL
     *
     * @var \League\Url\Url
     */
    protected $topicUrl;

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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //hub.mode has already been checked to be `publish`
        if (null !== $request->input('hub_url')) {
            $this->topicUrl = Url::createFromUrl($request->input('hub_url'));
            if (substr($this->topicUrl, 0, 2) == '//') {
                $this->topicUrl->setScheme('http');
            }
            Queue::push(new CheckTopic($this->topicUrl));
            return (new Response('Topic update notification accepted, thnanks.', 202));
        } else {
            return (new Response('You need to specify a topic URL', 400));
        }
    }
}
