<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('authenticatable');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_successful')->default(false);
            $table->string('event_type')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
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
        Schema::dropIfExists('auth_log');
    }
}