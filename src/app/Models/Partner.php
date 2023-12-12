<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    /**
     * @var string $primaryKey
     */
    protected $primaryKey = 'basic_cnpj';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'basic_cnpj',
        'partner_identifier',
        'partner_name',
        'cnpj_cpf_partner',
        'partner_qualification',
        'partnership_start_date',
        'country',
        'legal_representative',
        'representative_name',
        'representative_qualification',
        'age_group',
    ];
}
