<?php


namespace Motia\LaravelSesManager\Eloquent;


use Illuminate\Database\Eloquent\Model;

class BlackListGroup extends Model
{
  const TABLE_NAME = 'email_blacklist_groups';

  protected $table = self::TABLE_NAME;

  protected $fillable = [
    'driver',
    'reason',
    'bounced_at',
    'payload',
  ];

  protected $casts = [
    'bounced_at' => 'datetime',
    'payload' => 'array'
  ];

  public function blackListItems() {
    return $this->hasMany(BlackListItem::class, 'group_id');
  }
}
