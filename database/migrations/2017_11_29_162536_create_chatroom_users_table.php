<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatroomUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatroom_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chatroom_id', 30)->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('chatroom_id')->references('id')->on('chatrooms')->onDelete('cascade');
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
        Schema::dropIfExists('chatroom_user');
    }
}
