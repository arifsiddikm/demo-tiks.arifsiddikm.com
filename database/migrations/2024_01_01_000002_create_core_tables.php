<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // CITIES
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // CINEMAS (XXI locations)
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // GENRES
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // FILMS
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('trailer_photo')->nullable();
            $table->string('duration')->nullable(); // e.g. "120 min"
            $table->string('rating')->nullable(); // e.g. "13+", "R"
            $table->string('language')->default('Indonesia');
            $table->string('director')->nullable();
            $table->text('cast')->nullable(); // JSON or comma separated
            $table->date('release_date')->nullable();
            $table->enum('status', ['coming_soon', 'now_showing', 'ended'])->default('now_showing');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // FILM GENRES (pivot)
        Schema::create('film_genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
        });

        // FILM SCHEDULES
        Schema::create('film_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade');
            $table->foreignId('cinema_id')->constrained('cinemas')->onDelete('cascade');
            $table->date('show_date');
            $table->time('show_time');
            $table->string('studio')->default('Studio 1'); // Studio 1, 2, etc.
            $table->enum('film_type', ['2D', '3D', '4DX', 'IMAX'])->default('2D');
            $table->integer('total_seats')->default(96);
            $table->integer('available_seats')->default(96);
            $table->decimal('price', 10, 2)->default(50000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SEATS
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('film_schedules')->onDelete('cascade');
            $table->string('row'); // A, B, C...
            $table->integer('number'); // 1, 2, 3...
            $table->string('code'); // A1, A2, etc.
            $table->enum('status', ['available', 'booked', 'reserved'])->default('available');
            $table->timestamps();
        });

        // BOOKINGS
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('film_schedules')->onDelete('cascade');
            $table->integer('qty'); // number of tickets
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'waiting_payment', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_redeemed')->default(false);
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });

        // BOOKING SEATS
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('seat_id')->constrained('seats')->onDelete('cascade');
            $table->string('seat_code');
            $table->timestamps();
        });

        // NEWS
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('category')->default('Umum');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('booking_seats');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('film_schedules');
        Schema::dropIfExists('film_genres');
        Schema::dropIfExists('films');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('cinemas');
        Schema::dropIfExists('cities');
    }
};
