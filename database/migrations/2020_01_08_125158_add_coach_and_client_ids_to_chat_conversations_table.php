<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoachAndClientIdsToChatConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_conversations', static function (Blueprint $table) {
            $table->bigInteger('client_user_id')->unsigned()->after('id');
            $table->foreign('client_user_id')->references('id')->on('users');

            $table->bigInteger('coach_user_id')->unsigned()->after('id');
            $table->foreign('coach_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
