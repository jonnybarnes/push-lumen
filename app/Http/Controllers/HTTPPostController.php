<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class HTTPPostController extends Controller
{
    /**
     * Not sure on this, here we are checking a POST request to '/'
     * and checking for the `hub.mode` value, then calling the relavent
     * controller method, or returning a 400 response
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkMode(Request $request)
    {
        var_dump($request);
        if (null !== $request->input('hub_mode')) {
            switch ($request->input('hub_mode')) {
                case 'publish':
                    $t = new TopicsController();
                    $t->update($request);
                    break;
                
                default:
                    return (new Response('I don’t know this value for <code>hub.mode</code>', 501));
                    break;
            }
        } else {
            return (new Response('<code>hub.mode</code> must be set', 400));
        }
    }
}
