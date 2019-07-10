<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Motia\LaravelSesManager\Eloquent\BlackListGroup;
use Motia\LaravelSesManager\Eloquent\BlackListItem;

class CreateEmailBlacklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::transaction(function () {
        Schema::create(BlackListGroup::TABLE_NAME, function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('reason');
          $table->string('driver');
          $table->dateTime('bounced_at');
          $table->text('payload')->default('[]');

          $table->timestamps();
        });
        Schema::create(BlackListItem::TABLE_NAME, function (Blueprint $table) {
          $table->bigIncrements('id');

          $table->string('email');
          $table->dateTime('blacklisted_at')->nullable();
          $table->unsignedBigInteger('group_id');

          $table->foreign('group_id')->references('id')->on(BlackListGroup::TABLE_NAME);

          $table->timestamps();
          $table->softDeletes();
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
        Schema::dropIfExists(BlackListItem::TABLE_NAME);
        Schema::dropIfExists(BlackListGroup::TABLE_NAME);
      });
    }
}
