<?php namespace App\Repositories;

use DB;
use App\Interfaces\TopicRepositoryInterface;

class DBTopicRepository implements TopicRepositoryInterface
{
    public function all()
    {
        return DB::table('topics')->get();
    }

    public function getIdFromUrl($url)
    {
        $topicId = DB::table('topics')->where('url', $this->callbackUrl)->pluck('id');
        if ($topicId === null) {
            $topicId = DB::table('topics')->insertGetId(
                ['url' => $this->callbackUrl]
            );
        }

        return $topicId;
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
