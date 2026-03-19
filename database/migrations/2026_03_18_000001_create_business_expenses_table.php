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
        Schema::create('business_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 20);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('category')->comment('server, production, other');
            $table->text('description');
            $table->decimal('amount', 15, 0);
            $table->date('expense_date');
            $table->string('vendor_name')->nullable();
            $table->text('reference_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletesWithUser();

            $table->index('expense_number');
            $table->index('expense_date');
            $table->index('company_id');
            $table->index('category');
            $table->unique(['company_id', 'expense_number'], 'business_expenses_company_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_expenses');
    }
};
