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
}
