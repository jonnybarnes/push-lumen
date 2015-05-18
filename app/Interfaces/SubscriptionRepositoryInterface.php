<?php namespace App\Interfaces;

interface SubscriptionRepositoryInterface
{
    public function all();

    public function getIdFromUrls($topicId, $subscriberId);

    public function upsert($topicId, $subscriberId, $time);

    public function delete($topicId, $subscriberId);
}
