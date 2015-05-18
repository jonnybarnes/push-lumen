<?php namespace App\Interfaces;

interface SubscriberRepositoryInterface
{
    public function all();

    public function getIdFromUrl($callbackUrl);
}
