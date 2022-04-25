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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['text', 'image', 'video', 'audio', 'file']);
            $table->text('message');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('sender_id');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms');

            $table->foreign('sender_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
