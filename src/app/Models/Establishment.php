<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-extractor
 *
 * Created using PhpStorm at 23.12.23 08:55
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;
    protected $primaryKey = 'code';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'basic_cnpj',
        'cnpj_order',
        'cnpj_dv',
        'main_branch_office',
        'trade_name',
        'registration_status',
        'registration_status_date',
        'registration_reason',
        'foreign_city_name',
        'country',
        'activity_start_date',
        'main_cnae',
        'secondary_cnae',
        'street_type',
        'address',
        'address_number',
        'additional_address_info',
        'neighborhood',
        'zip_code',
        'state',
        'city_code',
        'phone_area_code_1',
        'phone_number_1',
        'phone_area_code_2',
        'phone_number_2',
        'fax_area_code',
        'fax_number',
        'email',
        'special_situation',
        'special_situation_date',
    ];
}
