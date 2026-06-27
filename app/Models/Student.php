<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nis',
        'name',
        'nasabah_type',
        'classroom_id',
        'status',
        'balance'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function paymentDetails()
    {
        return $this->hasManyThrough(PaymentDetail::class, Billing::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
