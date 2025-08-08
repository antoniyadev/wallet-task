<?php

namespace Tests\Unit\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /** @test */
    public function test_create_manual_credit_increases_user_balance()
    {
        $user    = User::factory()->create(['amount' => 1000]);
        $service = new TransactionService();

        $transaction = $service->createManualCredit($user, 500, 'Test credit');

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('credit', $transaction->type);
        $this->assertEquals(1500, $user->fresh()->amount);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount'  => 500,
            'type'    => 'credit',
        ]);
    }

    /** @test */
    public function test_create_manual_debit_decreases_user_balance()
    {
        $user    = User::factory()->create(['amount' => 2000]);
        $service = new TransactionService();

        $transaction = $service->createManualDebit($user, 800, 'Test debit');

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('debit', $transaction->type);
        $this->assertEquals(1200, $user->fresh()->amount);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount'  => 800,
            'type'    => 'debit',
        ]);
    }
}
