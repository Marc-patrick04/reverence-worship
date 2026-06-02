<?php

namespace App\Models\Intercession;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class ActionPlanTask extends Model
{
    protected $table = 'action_plan_tasks';
    
    protected $fillable = [
        'action_plan_id',
        'name',
        'due_date',
        'amount',
        'target',
        'timeline',
        'action_details',
        'status'
    ];
    
    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function actionPlan()
    {
        return $this->belongsTo(ActionPlan::class, 'action_plan_id');
    }
}