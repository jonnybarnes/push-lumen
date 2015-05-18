<?php namespace App\Repositories;

use DB;
use App\Interfaces\SubscriberRepositoryInterface;

class DBSubscriberRepository implements SubscriberRepositoryInterface
{
    public function all()
    {
        return DB::table('subscribers')->get();
    }

    public function getIdFromUrl($callbackUrl)
    {
        $subscriberId = DB::table('subscribers')->where('url', $callbackUrl)->pluck('id');
        if ($subscriberId === null) {
            $subscriberId = DB::table('subscribers')->insertGetId(
                ['url' => $callbackUrl]
            );
        }

        return $subscriberId;
    }
}
