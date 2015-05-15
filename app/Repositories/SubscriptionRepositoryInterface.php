<?php namespace App\Repositories;

interface SubscriptionRepositoryInterface
{
    public function all();

    public function getIdFromUrls($topic_id, $subscriber_id);

    public function upsert($topic_id, $subscriber_id, $time);
}
