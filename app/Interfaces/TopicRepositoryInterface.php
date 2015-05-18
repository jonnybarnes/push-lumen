<?php namespace App\Interfaces;

interface TopicRepositoryInterface
{
    public function all();

    public function getIdFromUrl($url);

    public function getSubscribers($url);
}
