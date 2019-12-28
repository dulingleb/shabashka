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
            $table->bigInteger('category_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('executor')->unsigned()->nullable();
            $table->enum('status', ['search_executor', 'doing', 'done'])->default('search_executor');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('executor')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('tasks');
        Schema::enableForeignKeyConstraints();
    }
}
