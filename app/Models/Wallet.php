<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // دالة للإيداع
    public function deposit(float $amount, string $description = null, string $reference = null)
    {
        $this->balance += $amount;
        $this->save();

        $this->transactions()->create([
            'amount' => $amount,
            'type' => 'deposit',
            'description' => $description,
            'reference' => $reference ?? uniqid(),
        ]);

        return $this;
    }

     public function withdraw(float $amount, string $description = null, string $reference = null)
    {
        DB::beginTransaction();

        try {
            if ($this->balance < $amount) {
                throw new \Exception('رصيد غير كافي');
            }

            $this->balance -= $amount;
            $this->save();

            $transaction = $this->transactions()->create([
                'amount' => $amount,
                'type' => 'withdrawal',
                'description' => $description,
                'reference' => $reference ?? uniqid(),
            ]);

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // دالة التحويل
    public function transfer(Wallet $recipient, float $amount, string $description = null)
    {
        DB::beginTransaction();

        try {
            if ($this->balance < $amount) {
                throw new \Exception('رصيد غير كافي للتحويل');
            }

            if ($this->id === $recipient->id) {
                throw new \Exception('لا يمكن التحويل لنفس المحفظة');
            }

            // سحب من المرسل
            $this->balance -= $amount;
            $this->save();

            // إيداع للمستلم
            $recipient->balance += $amount;
            $recipient->save();

            // تسجيل المعاملات
            $reference = uniqid();

            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'transfer_out',
                'description' => $description ?? 'تحويل إلى ' . $recipient->user->name,
                'reference' => $reference,
            ]);

            $recipient->transactions()->create([
                'amount' => $amount,
                'type' => 'transfer_in',
                'description' => $description ?? 'تحويل من ' . $this->user->name,
                'reference' => $reference,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
