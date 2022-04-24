<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('originvalues', function (Blueprint $table) {
      $table->dateTime('target_datetime');
      $table->string('service_number', 25);
      $table->string('dial_place', 5);
      $table->integer('all_calls');
      $table->integer('completion');
      $table->integer('wait_completion');
      $table->integer('completion_rate');
      $table->integer('not_completion');
      $table->integer('overtime');
      $table->integer('enconut');
      $table->integer('fd_busy');
      $table->integer('ls_busy');
      $table->integer('halfway_giveup');
      $table->integer('calling_giveup');
      $table->integer('unrespons_encount');
      $table->integer('talking_giveup');
      $table->integer('connection_denied');
      $table->integer('outside_region');
      $table->integer('rimitover');
      $table->integer('spam_filter');
      $table->integer('wait_giveup');
      $table->integer('wait_timeover');
      $table->integer('wait_rimitover');
      $table->integer('wait_retry');
      $table->integer('message_store');
      $table->integer('other');
      $table->string('average_talktime', 10);
      $table->string('waittime_finish', 10);
      $table->string('waittime_giveup', 10);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('originvalues');
  }
};
