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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users");
            $table->foreignId("room_id")->constrained("rooms");
            $table->decimal("amount", 10, 2);
            //$table->string("label");
            $table->date("start_date");
            $table->date("leased_until");
            $table->enum("type", ["Room", "Utilities", "N/A"])->default("N/A");
            $table->enum("status", ["Pending", "Approved", "Rejected"])->default("Pending");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
