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
        Schema::create('promotions', function (Blueprint $table) use ($driver) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->string('title');
            $table->text('content');
            $table->json('metadata');

            switch ($driver) {
                case 'mysql':
                case 'sqlite':
                case 'pgsql':
                    $valid = DB::connection()->getQueryGrammar()->wrap('metadata->valid_to');
                    $table->timestamp('valid_to')->storedAs($valid);
                    break;

                case 'sqlsrv':
                    $valid = DB::connection()->getQueryGrammar()->wrap('metadata->valid_to');
                    $valid_to = 'CAST(' . $valid . ' AS DATETIME)';
                    $table->computed('valid_to', $valid_to)->persisted();
                    break;
            }


            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
