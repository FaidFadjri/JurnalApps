<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transaction extends Migration
{
    public function up()
    {
        //---- Transaction code as account code
        Schema::create('user', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->string('username')->unique()->primary();
            $table->string('password');
            $table->enum('role', ['editor', 'viewer']);
        });


        //---- Transaction code as account code
        Schema::create('transaction_code', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->string('code', 250)->unique()->primary()->index();
            $table->string('keterangan');
        });

        //---- Tabel PKB
        Schema::create('transaction_pkb', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->id()->autoIncrement();
            $table->string('wo');
            $table->string('license_plate');
            $table->string('customer');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        //---- Tabel Kredit
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->id()->autoIncrement();
            $table->integer('jasa');
            $table->integer('parts');
            $table->integer('bahan');
            $table->integer('OPL');
            $table->integer('OPB');
            $table->string('kode_jasa');

            $table->string('kode_parts');
            $table->string('kode_bahan');
            $table->string('kode_opl');
            $table->string('kode_opb');


            //---- Discount Column
            $table->integer('discJasa');
            $table->integer('discParts');
            $table->integer('discBahan');
            $table->integer('discOPL');
            $table->integer('discOPB');
            $table->string('kode_discJasa');
            $table->string('kode_discParts');
            $table->string('kode_discBahan');
            $table->string('kode_discOpl');
            $table->string('kode_discOpb');
            $table->integer('ppn');
            $table->string('kode_ppn');
            $table->string('total');
            $table->string('kode_total');
            $table->unsignedBigInteger('id_pkb');
            $table->foreign('id_pkb')->references('id')->on('transaction_pkb')->onDelete('cascade');
            $table->date('invoice_date');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }
    public function down()
    {
        Schema::drop('transaction_code');
        Schema::drop('transaction_pkb');
        Schema::drop('transaction_detail');
    }
}
