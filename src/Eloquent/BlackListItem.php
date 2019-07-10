<?php


namespace Motia\LaravelSesManager\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlackListItem extends Model
{
  use SoftDeletes;

  const TABLE_NAME = 'email_blacklist_items';

  protected $table = self::TABLE_NAME;

  protected $casts = [
    'blacklisted_at' => 'datetime',
  ];

  /**
   * Scope a query to only include popular users.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopePending($query) {
    return $query->whereNull('blacklisted_at');
  }

  public function blackListGroup() {
    return $this->belongsTo(BlackListGroup::class, 'group_id');
  }
}
