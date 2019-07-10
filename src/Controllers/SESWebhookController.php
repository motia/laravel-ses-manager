<?php


namespace Motia\LaravelSesManager\Controllers;

use Motia\LaravelSesManager\Contracts\SESMessageValidatorContract;
use Motia\LaravelSesManager\Jobs\HandleSESBounce;
use Motia\LaravelSesManager\Jobs\HandleSESComplaint;
use Motia\LaravelSesManager\SESConfirmWebhookMiddleware;
use Motia\LaravelSesManager\SESMessageValidator;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SESWebhookController extends Controller
{
  use DispatchesJobs;
  
  /**
   * @var SESMessageValidator
   */
  private $SESMessageValidator;

  public function __construct(SESMessageValidatorContract $SESMessageValidator)
  {
    $this->SESMessageValidator = $SESMessageValidator;
    $this->middleware(SESConfirmWebhookMiddleware::class);
  }

  /**
   * @return string
   * @throws WrongWebhookRouting
   * @throws \Motia\LaravelSesManager\Exceptions\SesConfirmationFailed
   */
  public function bounce() {
    $message = $this->getMessageOfType('Bounce');

    $this->dispatchNow(new HandleSESBounce($message));

    return new Response('handled', 200);
  }

  /**
   * @throws WrongWebhookRouting
   */
  public function complaint() {
    $message = $this->getMessageOfType('Complaint');

    $this->dispatchNow(new HandleSESComplaint(
      $message
    ));

    return new Response('handled', 200);
  }

  /**
   * @param string $string
   * @return mixed
   * @throws WrongWebhookRouting
   */
  private function getMessageOfType(string $string)
  {
    return $this->SESMessageValidator->getMessageOfType($string);
  }
}
