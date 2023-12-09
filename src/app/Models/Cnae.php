<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 07.12.23 01:21
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cnae extends Model
{
    protected $primaryKey = 'code';
    protected $fillable = [
        'code',
        'name'
    ];
}