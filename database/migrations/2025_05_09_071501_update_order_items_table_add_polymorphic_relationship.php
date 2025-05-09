<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('orderable_type')->nullable()->after('book_id');
            $table->unsignedBigInteger('orderable_id')->nullable()->after('orderable_type');
        });

        DB::table('order_items')->whereNotNull('book_id')->update([
            'orderable_type' => Book::class,
            'orderable_id' => DB::raw('book_id')
        ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('book_id')->nullable()->after('order_id');

            DB::table('order_items')
                ->where('orderable_type', Book::class)
                ->update(['book_id' => DB::raw('orderable_id')]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['orderable_type', 'orderable_id']);
        });
    }
};
