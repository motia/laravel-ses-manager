<?php


namespace Motia\LaravelSesManager\Exceptions;


class WrongWebhookRouting extends \Exception
{
  public function __construct(string $driver, string $action, array $payload)
  {
    $json = json_encode($payload);
    $action = ucfirst($action);
    parent::__construct("$action webhook for driver $driver received wrong payload:\n $json");
  }
}