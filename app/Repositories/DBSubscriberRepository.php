<?php namespace App\Repositories;

use DB;
use App\Repostories\SubscriberRepositoryInterface;

class DBSubscriberRepository implements SubscriberRepositoryInterface
{
    public function all()
    {
        return DB::table('subscribers')->get();
    }

    public function getIdFromUrl($callbackUrl)
    {
        $subscriber_id = DB::table('subscribers')->where('url', $callbackUrl)->pluck('id');
        if ($subscriber_id === null) {
            $subscriber_id = DB::table('subscribers')->insertGetId(
                ['url' => $callbackUrl]
            );
        }

        return $subscriber_id;
    }
}
