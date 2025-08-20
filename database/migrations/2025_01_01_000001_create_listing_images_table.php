<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('listing_images', function (Blueprint $table) {
      $table->id();
      $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
      $table->string('path');
      $table->boolean('is_primary')->default(false);
      $table->unsignedTinyInteger('ordering')->default(0);
      $table->timestamps();
      $table->index(['listing_id','is_primary']);
    });
  }
  public function down(): void { Schema::dropIfExists('listing_images'); }
};
