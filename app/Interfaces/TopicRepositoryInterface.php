<?php namespace App\Interfaces;

interface TopicRepositoryInterface
{
    /**
     * Get all the topics
     *
     * @return Iterable
     */
    public function all();

    /**
     * Get the id of a topic from its URL
     *
     * @param  string
     * @return int
     */
    public function getIdFromUrl($url);

    /**
     * Get a list of subscribers to a topic from its URL
     *
     * @param  string
     * @return Iterable
     */
    public function getSubscribers($url);
}
