<?php


namespace Motia\LaravelSesManager\Eloquent;

use Illuminate\Database\Eloquent\Model;

class MailComplaint extends Model
{
  const TABLE_NAME = 'email_complaints';

  protected $table = self::TABLE_NAME;

  public function complaintGroup(){
    return $this->belongsTo(MailComplaintGroup::class);
  }
}