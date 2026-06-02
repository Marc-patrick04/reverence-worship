<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class ContributionSetting extends Model
{
    protected $table = 'contribution_settings';
    
    protected $fillable = [
        'year', 'term1_amount', 'term2_amount', 'term3_amount', 'term4_amount', 'is_active', 'updated_by'
    ];
    
    public function getTermAmount($term)
    {
        $field = 'term' . $term . '_amount';
        return $this->$field ?? 0;
    }
}