<?php


namespace Motia\LaravelSesManager\Tests\Unit;


use Motia\LaravelSesManager\Eloquent\MailComplaintGroup;
use Motia\LaravelSesManager\Jobs\HandleSESComplaint;
use Motia\LaravelSesManager\Tests\TestCase;

class HandleSESComplaintTest extends TestCase
{
  const VALID_COMPLAINT_JSON = "{
       \"userAgent\":\"AnyCompany Feedback Loop (V0.01)\",
       \"complainedRecipients\":[
          {
             \"emailAddress\":\"recipient1@example.com\"
          },
          {
             \"emailAddress\":\"recipient2@example.com\"
          },
          {}
       ],
       \"complaintFeedbackType\":\"abuse\",
       \"arrivalDate\":\"2009-12-03T04:24:21.000-05:00\",
       \"timestamp\":\"2012-05-25T14:59:38.623Z\",
       \"feedbackId\":\"000001378603177f-18c07c78-fa81-4a58-9dd1-fedc3cb8f49a-000000\"
    }";

  public function setUp(): void
  {
    parent::setUp();
  }

  public function testSavesItemsCorrectly() {
    $message = ['complaint' => json_decode(self::VALID_COMPLAINT_JSON, true)];
    $job = new HandleSESComplaint($message);
    $job->handle();

    $blgs = MailComplaintGroup::query()->get();
    $this->assertCount(1, $blgs);
    $this->assertEquals('abuse', $blgs[0]->reason);

    $r = collect($blgs[0]->complaints)->sortBy('email')->map->email->toArray();
    $this->assertEquals(
      $r,
      ['recipient1@example.com', 'recipient2@example.com']
    );
  }
}