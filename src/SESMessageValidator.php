<?php


namespace Motia\LaravelSesManager;


use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Support\Facades\Log;
use Motia\LaravelSesManager\Contracts\SESMessageValidatorContract;
use Motia\LaravelSesManager\Exceptions\SesConfirmationFailed;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;
use GuzzleHttp;

class SESMessageValidator implements SESMessageValidatorContract
{
  public function getMessageOfType($type) {
    $notification = $this->getNotificationOfType('Notification');
    $messageStr = $notification['Message'];

    if (!$messageStr) {
      $str = json_encode($notification);
      throw new \RuntimeException("Malformed notification: $str");
    }

    $message = json_decode($messageStr, true);

    if(($message['notificationType'] ?? null) !== $type) {
      throw new WrongWebhookRouting(
        'ses',
        $type,
        $message
      );
    }

    return $message;
  }

  public function getConfirmationMessage() {
    return $this->getNotificationOfType('SubscriptionConfirmation');
  }

  /**
   * @param array $message
   * @throws SesConfirmationFailed
   */
  public function confirmSubscription(array $message) {
    $ch = curl_init($message['SubscribeURL']);
    curl_exec($ch);

    if (!curl_errno($ch)) {
      throw new SesConfirmationFailed();
    }
  }

  /**
   * @param string $type
   * @return array mixed
   */
  private function getNotificationOfType($type)
  {
    if (!in_array($type, ['SubscriptionConfirmation', 'Notification'])) {
      throw new \InvalidArgumentException("Type $type is wrong");
    }
    $message = Message::fromRawPostData();
    if (config('ses-manager.use_validator') && !config('ses-manager.payload_only')) {
      $this->validateSnsMessage();
    }

    $message = $message->toArray();

    if ($message['Type'] !== $type) {
      return null;
    }

    return $message;
  }

  protected function validateSnsMessage()
  {
    $validator = new MessageValidator();
    // Validate the message
    try {
      $validator->validate($message);
    } catch (InvalidSnsMessageException $e) {
      Log::error('SNS Message Validation Error: ' . $e->getMessage());
      throw $e;
    }
  }
}
