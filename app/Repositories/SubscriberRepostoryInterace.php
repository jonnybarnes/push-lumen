<?php namespace App\Repositories;

interface SubscriberRepositoryInterface
{
    public function all();

    public function getIdFromUrl($callbackUrl);
}
