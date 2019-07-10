<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Motia\LaravelSesManager\Eloquent\MailComplaint;
use Motia\LaravelSesManager\Eloquent\MailComplaintGroup;

class CreateEmailComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::transaction(function () {
        Schema::create(MailComplaintGroup::TABLE_NAME, function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('reason');
          $table->string('driver');
          $table->dateTime('complained_at');
          $table->text('payload')->default('[]');

          $table->timestamps();
        });
        Schema::create(MailComplaint::TABLE_NAME, function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('email');
          $table->unsignedBigInteger('group_id');

          $table->foreign('group_id')->references('id')->on(MailComplaintGroup::TABLE_NAME);

          $table->timestamps();
        });
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::transaction(function () {
        Schema::dropIfExists(MailComplaint::TABLE_NAME);
        Schema::dropIfExists(MailComplaintGroup::TABLE_NAME);
      });
    }
}
