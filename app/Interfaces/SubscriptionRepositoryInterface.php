<?php namespace App\Interfaces;

interface SubscriptionRepositoryInterface
{
    /**
     * Get all the known subscriptions
     *
     * @return Iterable
     */
    public function all();

    /**
     * Get the id of subscription
     *
     * @param  int
     * @param  int
     * @return int
     */
    public function getIdFromUrls($topicId, $subscriberId);

    /**
     * Update an existing subscription $time or create a new subscription
     *
     * @param  int
     * @param  int
     * @param  int
     * @return bool
     */
    public function upsert($topicId, $subscriberId, $time);

    /**
     * Delete a subscription
     *
     * @param  int
     * @param  int
     * @return bool
     */
    public function delete($topicId, $subscriberId);
}
