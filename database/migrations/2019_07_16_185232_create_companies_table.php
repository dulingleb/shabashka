<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index()->unsigned();
            $table->boolean('is_active')->default(0);
            $table->enum('moderate_status', ['process', 'success', 'rejected'])->default('process');
            $table->string('reject_message')->nullable();
            $table->string('title');
            $table->string('inn', 12);
            $table->string('address');
            $table->text('description')->nullable();
            $table->text('documents')->nullable();
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table){
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
        Schema::dropIfExists('companies');
        Schema::enableForeignKeyConstraints();
    }
}
