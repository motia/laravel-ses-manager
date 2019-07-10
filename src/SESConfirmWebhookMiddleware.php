<?php


namespace Motia\LaravelSesManager;

use Illuminate\Http\Request;
use Motia\LaravelSesManager\Contracts\SESMessageValidatorContract;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;
use Symfony\Component\HttpFoundation\Response;

class SESConfirmWebhookMiddleware
{
  /**
   * @var SESMessageValidatorContract
   */
  private $messageValidatorContract;

  public function __construct(SESMessageValidatorContract $messageValidatorContract)
  {
    $this->messageValidatorContract = $messageValidatorContract;
  }

  /**
   * @param Request $request
   * @param $next
   * @return string
   * @throws Exceptions\SesConfirmationFailed
   */
  function handle (Request $request, $next) {
    $message = $this->messageValidatorContract->getConfirmationMessage();

    if ($message) {
      $this->messageValidatorContract->confirmSubscription($message);
      return new Response('confirmed', 200);
    }
    return $next($request);
  }
}
