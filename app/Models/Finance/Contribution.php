<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Contribution extends Model
{
    protected $table = 'contributions';
    
    protected $fillable = [
        'user_id', 'term', 'year', 'amount', 'status', 
        'payment_date', 'payment_method', 'transaction_id', 
        'notes', 'submitted_by', 'approved_by', 'approved_at'
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}