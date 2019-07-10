<?php


namespace Motia\LaravelSesManager\Jobs;


use Illuminate\Support\Facades\DB;
use Motia\LaravelSesManager\Eloquent\BlackListGroup;
use Motia\LaravelSesManager\Eloquent\BlackListItem;

class HandleSESBounce
{
  public $message;

  public function __construct($message)
  {
    $this->message = $message;
  }

  public function handle() {
    $bounce = $this->message['bounce'];
    $bouncedEmails = collect($bounce['bouncedRecipients']);
    $bouncedAt = \Carbon\Carbon::parse($bounce['timestamp']);
//    unset($message['bounce']['bouncedRecipients'], $this->message['bounce']['timestamp']);

    $useManualBlackList = $bounce['bounceType'] === 'Transient'
      && $bounce['bounceSubType'] !== 'AttachmentRejected';


    DB::beginTransaction();
    $batchId = BlackListGroup::query()->create([
      'driver' => 'ses',
      'reason' => $bounce['bounceSubType'],
      'bounced_at' => $bouncedAt,
      'payload' => $this->message,
    ])->id;

    $now = now();
    $data = $bouncedEmails->map(
      function ($x) use ($useManualBlackList, $now, $batchId) {
        return $data[] = [
          'email' => $x['emailAddress'],
          'blacklisted_at' => $useManualBlackList ? null : $now,
          'group_id' => $batchId,
          'created_at' => $now,
          'updated_at' => $now,
        ];
      })->toArray();
    BlackListItem::query()->insert($data);
    DB::commit();
  }
}
