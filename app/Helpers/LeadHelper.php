<?php

namespace App\Helpers;

class LeadHelper
{
    public static function getStatusColor($status)
    {
        $colors = [
            'new' => 'primary',
            'contacted' => 'info',
            'qualified' => 'warning',
            'converted' => 'success',
            'lost' => 'danger',
            'shared' => 'secondary'
        ];
        
        return $colors[$status] ?? 'primary';
    }
} 