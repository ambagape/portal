<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptChatMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('chat_messages')
            ->select('id', 'message')
            ->orderBy('id')
            ->each(function ($m) {
                DB::table('chat_messages')
                    ->where('id', $m->id)
                    ->update([
                        'message' => Crypt::encryptString($m->message),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('chat_messages')
            ->select('id', 'message')
            ->orderBy('id')
            ->each(function ($m) {
                DB::table('chat_messages')
                    ->where('id', $m->id)
                    ->update([
                        'message' => Crypt::decryptString($m->message),
                    ]);
            });
    }
}
