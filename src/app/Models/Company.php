<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 07.12.23 02:18
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $primaryKey = 'basic_cnpj';
    protected $fillable = [
        'basic_cnpj',
        'corporate_name',
        'legal_nature',
        'responsible_qualification',
        'capital_social',
        'company_size',
        'federative_entity_responsible'
    ];
}
