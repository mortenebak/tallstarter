<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('features')->nullable();
            $table->string('interval')->nullable();
            $table->string('currency')->nullable();
            $table->string('price')->nullable();
            $table->string('price_description')->nullable();
            $table->string('description')->nullable();
            $table->string('stripe_id')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
