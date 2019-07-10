<?php


namespace Motia\LaravelSesManager\Jobs;


use Illuminate\Support\Facades\DB;
use Motia\LaravelSesManager\Eloquent\MailComplaint;
use Motia\LaravelSesManager\Eloquent\MailComplaintGroup;

class HandleSESComplaint
{
  public $message;

  public function __construct($message)
  {
    $this->message = $message;
  }

  public function handle() {
    $complaint = $this->message['complaint'];
    $complainers = collect($complaint['complainedRecipients']);
    $complainTimestamp = \Carbon\Carbon::parse($complaint['timestamp']);

//    unset($message['complaint']['complainedRecipients'], $message['complaint']['timestamp']);

    DB::beginTransaction();
    $groupId = MailComplaintGroup::query()->create([
      'driver' => 'ses',
      'complained_at' => $complainTimestamp,
      'payload' => $this->message,
      'reason' => $complaint['complaintFeedbackType'],
    ])->id;

    $now = now();
    $r = [];
    foreach ($complainers as $complainer) {
      if ($complainer['emailAddress'] ?? false) {
        $r[] = [
          'email' => $complainer['emailAddress'],
          'group_id' => $groupId,
          'created_at' => $now,
          'updated_at' => $now,
        ];
      }
    }
    MailComplaint::query()->insert($r);

    DB::commit();
  }
}
