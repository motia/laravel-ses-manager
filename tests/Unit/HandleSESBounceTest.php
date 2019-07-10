<?php


namespace Motia\LaravelSesManager\Tests\Unit;


use Motia\LaravelSesManager\Eloquent\BlackListGroup;
use Motia\LaravelSesManager\Jobs\HandleSESBounce;
use Motia\LaravelSesManager\Tests\TestCase;

class HandleSESBounceTest extends TestCase
{
  const VALID_BOUNCE_JSON = "{
       \"bounceType\":\"Permanent\",
       \"bounceSubType\": \"General\",
       \"bouncedRecipients\":[
          {
             \"status\":\"5.0.0\",
             \"action\":\"failed\",
             \"diagnosticCode\":\"smtp; 550 user unknown\",
             \"emailAddress\":\"recipient1@example.com\"
          },
          {
             \"status\":\"4.0.0\",
             \"action\":\"delayed\",
             \"emailAddress\":\"recipient2@example.com\"
          }
       ],
       \"reportingMTA\": \"example.com\",
       \"timestamp\":\"2012-05-25T14:59:38.605Z\",
       \"feedbackId\":\"000001378603176d-5a4b5ad9-6f30-4198-a8c3-b1eb0c270a1d-000000\",
       \"remoteMtaIp\":\"127.0.2.0\"
    }";

  public function setUp(): void
  {
    parent::setUp();
  }

  public function testSavesItemsCorrectly() {
    $message = ['bounce' => json_decode(self::VALID_BOUNCE_JSON, true)];
    $job = new HandleSESBounce($message);
    $job->handle();

    $blgs = BlackListGroup::query()->get();
    $this->assertCount(1, $blgs);
    $this->assertEquals('General', $blgs[0]->reason);

    $this->assertEquals(
      collect($blgs[0]->blackListItems)->sortBy('email')->map->email->toArray(),
      ['recipient1@example.com', 'recipient2@example.com']
    );
  }
}
