<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'student_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
