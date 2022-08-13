<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->bigIncrements('id',25)->start_from(10000)->unique();
            $table->json('emisor');
            $table->json('comprador');
            $table->string('valor_antes_iva',15);
            $table->string('iva',15);
            $table->string('valor_a_pagar',15);
            $table->json('items_facturados');
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
        Schema::dropIfExists('facturas');
    }
};
