<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'student_id', 'invoice_no', 'description', 'amount',
        'payment_method', 'status', 'reference', 'hostel_fee_id',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function hostelFee()
    {
        return $this->belongsTo(HostelFee::class);
    }

    /**
     * Generate a unique invoice number like INV-2026-00042
     */
    public static function generateInvoiceNo(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        return sprintf('INV-%s-%05d', $year, $last + 1);
    }
}
