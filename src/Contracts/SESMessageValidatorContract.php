<?php


namespace Motia\LaravelSesManager\Contracts;

use Motia\LaravelSesManager\Exceptions\SesConfirmationFailed;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;

interface SESMessageValidatorContract
{
  /**
   * @param string $type
   * @return array mixed
   * @throws WrongWebhookRouting
   */
  public function getMessageOfType($type);

  /**
   * @param array $message
   * @throws SesConfirmationFailed
   */
  public function confirmSubscription(array $message);
}