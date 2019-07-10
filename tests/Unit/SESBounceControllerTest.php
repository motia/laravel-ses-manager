<?php


namespace Motia\LaravelSesManager\Tests\Unit;


use Illuminate\Http\Request;
use Mockery\Mock;
use Motia\LaravelSesManager\Contracts\SESMessageValidatorContract;
use Motia\LaravelSesManager\Controllers\SESWebhookController;
use Motia\LaravelSesManager\SESConfirmWebhookMiddleware;
use Motia\LaravelSesManager\SESMessageValidator;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;
use Motia\LaravelSesManager\Tests\TestCase;

class SESBounceControllerTest extends TestCase
{
  public function testComplainFailOnWrongMessage() {
    /**
     * @var SESMessageValidatorContract|Mock $failingValidator
     */
    $failingValidator = \Mockery::mock(
      SESMessageValidator::class
    );

    $failingValidator->shouldReceive('getMessageOfType')
      ->andThrow(new WrongWebhookRouting('ses', 'confirm', []));
    $this->expectException(WrongWebhookRouting::class);

    (new SESWebhookController($failingValidator))->complaint();
  }
  
  public function testBounceFailOnWrongMessage() {
    /**
     * @var SESMessageValidatorContract|Mock $failingValidator
     */
    $failingValidator = \Mockery::mock(
      SESMessageValidator::class
    );

    $failingValidator->shouldReceive('getMessageOfType')
      ->andThrow(new WrongWebhookRouting('ses', 'confirm', []));
    $this->expectException(WrongWebhookRouting::class);

    (new SESWebhookController($failingValidator))->bounce();
  }
}
