<?php namespace App\Repositories;

use DB;
use App\Repostories\TopicRepositoryInterface;

class DBTopicRepository implements TopicRepositoryInterface
{
    public function all()
    {
        return DB::table('topics')->get();
    }

    public function getIdFromUrl($url)
    {
        $topic_id = DB::table('topics')->where('url', $this->callbackUrl)->pluck('id');
        if ($topic_id === null) {
            $topic_id = DB::table('topics')->insertGetId(
                ['url' => $this->callbackUrl]
            );
        }

        return $topic_id;
    }

    public function getSubscribers($url)
    {
        $subs = DB::table('topics')
                ->where('url', '=', $url)
                ->join('subscriptions', 'topics.id', '=', 'subscriptions.topic_id')
                ->join('subscribers', 'subscriptions.subscriber_id', '=', 'subscribers.id')
                ->get();
        return $subs;
    }
}
