<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payer_name');
            $table->string('payer_email');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('tran_ref_no')->nullable()->unique();
            $table->string('pay_ref_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('gateway_code')->nullable();
            $table->string('gateway_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'payer_name',
                'payer_email',
                'amount',
                'status',
                'tran_ref_no',
                'pay_ref_no',
                'receipt_no',
                'gateway_code',
                'gateway_message',
            ]);
        });
    }
}
