<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        Schema::create('products', function (Blueprint $table) use ($driver) {
            $table->bigIncrements('id');
            $table->foreignUuid('category_uuid')->constrained('categories', 'uuid')->cascadeOnDelete();
            $table->string('uuid')->unique();
            $table->string('title');
            $table->float('price', 12);
            $table->text('description');
            $table->json('metadata');

            switch ($driver) {
                case 'mysql':
                case 'sqlite':
                case 'pgsql':
                    $brand = DB::connection()->getQueryGrammar()->wrap('metadata->brand');
                    $table->uuid('brand_uuid')->storedAs($brand);
                    $table->foreign('brand_uuid')->references('uuid')->on('brands')->cascadeOnDelete();
                    break;

                case 'sqlsrv':
                    $brand = DB::connection()->getQueryGrammar()->wrap('metadata->brand');
                    $brand_uuid = 'CAST(' . $brand . ' AS VARCHAR)';
                    $table->computed('brand_uuid', $brand_uuid)->persisted();
                    $table->foreign('brand_uuid')->references('uuid')->on('brands')->cascadeOnDelete();
                    break;
            }
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
