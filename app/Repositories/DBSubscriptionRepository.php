<?php namespace App\Repositories;

use DB;
use App\Interfaces\SubscriptionRepositoryInterface;

class DBTopicRepository implements SubscriptionRepositoryInterface
{
    public function all()
    {
        return DB::table('topics')->get();
    }

    public function getIdFromUrls($topicId, $subscriberId)
    {
        $sub = DB::table('subscriptions')
                    ->where('topic_id', '=', $topicId)
                    ->where('subscriber_id', '=', $subscriberId)
                    ->first();
        return $sub;
    }

    public function upsert($topicId, $subscriberId, $time = null)
    {
        $this->time = $time ?: time();
        $sub = DB::table('subscriptions')
                 ->where('topic_id', '=', $topicId)
                 ->where('subscriber_id', '=', $subscriberId)
                 ->first();
        if ($sub === null) {
            //new subscription
            DB::table('subscriptions')->insert(
                [
                    'topic_id' => $topicId,
                    'subscriber_id' => $subscriberId,
                    'last_checked' => $this->time;
                ]
            );
            return true;
        } else {
            //updated subscription
            DB::table('subscriptions')
              ->where('topic_id', '=', $topicId)
              ->where('subscriber_id', '=', $subscriberId)
              ->update(['last_checked', $this->time]);
            return true;
        }
    }

    public function delete($topicId, $subscriberId)
    {
        DB::table('subscriptions')
            ->where('subscriber_id', '=', $subscriberId)
            ->where('topic_id', '=', $topicId)
            ->delete();
        return true;
    }
}
