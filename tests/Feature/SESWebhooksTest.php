<?php


namespace Motia\LaravelSesManager\Tests\Feature;


use Illuminate\Support\Facades\Bus;
use Motia\LaravelSesManager\Contracts\SESMessageValidatorContract;
use Motia\LaravelSesManager\Jobs\HandleSESBounce;
use Motia\LaravelSesManager\Jobs\HandleSESComplaint;
use Motia\LaravelSesManager\Exceptions\WrongWebhookRouting;
use Motia\LaravelSesManager\Tests\TestCase;

class SESWebhooksTest extends TestCase
{
  public function testBounceWebhook() {
    list($dummy, $message)= $this->prepareDummyValidatorAndMessage('Bounce');

    $this->app->instance(
      SESMessageValidatorContract::class,
      $dummy
    );

    Bus::fake();
    $this->postJson('/api/webhooks/ses/bounce')
      ->assertSuccessful();

    Bus::assertDispatched(HandleSESBounce::class, function ($job) use ($message) {
      return $job->message === $message;
    });
  }

  public function testComplaintWebhook() {
    list($dummy, $message) = $this->prepareDummyValidatorAndMessage('Bounce');

    $dummy->shouldReceive(
      'getMessageOfType'
      )
      ->with('Complaint')
      ->andReturn($message);

    $this->app->instance(
      SESMessageValidatorContract::class,
      $dummy
    );

    Bus::fake();
    $this->postJson('/api/webhooks/ses/complaint')
      ->assertSuccessful();

    Bus::assertDispatched(HandleSESComplaint::class, function ($job) use ($message) {
      return $job->message === $message;
    });
  }

  private function prepareDummyValidatorAndMessage($type)
  {
    $dummy = \Mockery::mock(SESMessageValidatorContract::class);
    $dummy->shouldReceive(
      'getConfirmationMessage'
    )
      ->andReturns();

    $message = [
      'notificationType' => $type,
    ];
    $dummy->shouldReceive(
      'getMessageOfType'
    )
      ->with($type)
      ->andReturn($message);

    return [$dummy, $message];
  }
}
