<?php namespace App\Repositories;

use DB;
use App\Repostories\SubscriptionRepositoryInterface;

class DBTopicRepository implements SubscriptionRepositoryInterface
{
    public function all()
    {
        return DB::table('topics')->get();
    }

    public function getIdFromUrls($topic_id, $subscriber_id)
    {
        $sub = DB::table('subscriptions')
                    ->where('topic_id', '=', $topic_id)
                    ->where('subscriber_id', '=', $subscriber_id)
                    ->first();
        return $sub;
    }

    public function upsert($topic_id, $subscriber_id, $time)
    {
        $sub = DB::table('subscriptions')
                 ->where('topic_id', '=', $topic_id)
                 ->where('subscriber_id', '=', $subscriber_id)
                 ->first();
        if ($sub === null) {
            //new subscription
            DB::table('subscriptions')->insert(
                [
                    'topic_id' => $topic_id,
                    'subscriber_id' => $subscriber_id,
                    'last_checked' => time()
                ]
            );
            return true;
        } else {
            //updated subscription
            DB::table('subscriptions')
              ->where('topic_id', '=', $topic_id)
              ->where('subscriber_id', '=', $subscriber_id)
              ->update(['last_checked', time()]);
            return true;
        }
    }

    public function delete($topic_id, $subscriber_id)
    {
        DB::table('subscriptions')
            ->where('subscriber_id', '=', $subscriber_id)
            ->where('topic_id', '=', $topic_id)
            ->delete();
        return true;
    }
}
