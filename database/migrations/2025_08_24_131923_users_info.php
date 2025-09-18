<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId'); // agar ye foreign key hai
            $table->string('fullname');
            $table->string('address');
            $table->string('address1')->nullable(); // optional banaya
            $table->string('phone', 20);
            $table->string('uphone', 20)->nullable(); // optional
            $table->string('zipcode', 10);
            $table->string('landmark')->nullable();
            $table->timestamps(); // created_at & updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_info');
    }
};
