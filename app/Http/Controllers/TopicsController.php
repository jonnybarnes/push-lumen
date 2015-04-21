<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class TopicsController extends Controller
{
    public function update(Request $request)
    {
        $mode = $request->input('hub_mode');
        $topicUrl = $request->input('hub_url');
        Queue::push(new CheckTopic($topicUrl));
        return (new Response('Topic update notification accepted, thnanks.', 202));
    }
}
