<?php namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    /**
     * Here we simply queue the job of checking the topic url for updates
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $mode = $request->input('hub_mode');
        $topicUrl = $request->input('hub_url');
        Queue::push(new CheckTopic($topicUrl));
        return (new Response('Topic update notification accepted, thnanks.', 202));
    }
}
