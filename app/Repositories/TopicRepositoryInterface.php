<?php namespace App\Repositories;

interface TopicRepositoryInterface
{
    public function all();

    public function getIdFromUrl($url);

    public function getSubscribers($url);
}
