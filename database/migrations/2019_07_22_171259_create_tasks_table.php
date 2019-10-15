<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 100);
            $table->text('description');
            $table->text('files')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('term');
            $table->float('price', 10, 0)->default(0);
            $table->string('phone');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('user_id')->unsigned();
            $table->integer('executor')->unsigned()->nullable();
            $table->string('status', 255)->default('wait');
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
        Schema::dropIfExists('tasks');
    }
}
