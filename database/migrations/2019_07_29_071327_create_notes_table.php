<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('notes', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('contact_id');
      $table->string('title');
      $table->string('text', 5000);
      $table->timestamps();
      $table->engine = 'memory';
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('notes');
  }
}
