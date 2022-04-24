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
    Schema::create('consoledialbases', function (Blueprint $table) {
      $table->increments('id');
      $table->string('serveice_number', 25);
      $table->string('base_id', 20);
      $table->string('base_number', 25);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('consoledialbases');
  }
};
