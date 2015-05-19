<?php namespace App\Interfaces;

interface SubscriberRepositoryInterface
{
    /**
     * Get all the subscribers
     *
     * @return Iterable
     */
    public function all();

    /**
     * Get the id of a subscriber from the subscriber’s callback URL
     *
     * @param  string
     * @return int
     */
    public function getIdFromUrl($callbackUrl);
}
