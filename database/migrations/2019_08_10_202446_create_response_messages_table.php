<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResponseMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('response_id')->unsigned()->index();
            $table->integer('user_id')->unsigned();
            $table->text('text');
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('responses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('response_messages');
        Schema::enableForeignKeyConstraints();
    }
}
