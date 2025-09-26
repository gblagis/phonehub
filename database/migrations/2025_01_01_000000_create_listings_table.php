<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('os', ['iOS', 'Android'])->nullable();
            $table->enum('condition', ['New', 'Like New', 'Good', 'Fair', 'Needs Repair']);
            $table->string('color')->nullable();
            $table->string('city');
            $table->text('description')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['active', 'sold', 'archived', 'pending'])->default('active');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['brand', 'model']);
            $table->index('price');
            $table->index('city');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
