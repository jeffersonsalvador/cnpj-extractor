<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-extractor
 *
 * Created using PhpStorm at 24.12.23 00:16
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simple extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'basic_cnpj';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var string[]
     */
    protected $fillable = [
        'basic_cnpj',
        'simple_option',
        'simple_option_date',
        'simple_exclusion_date',
        'mei_option',
        'mei_option_date',
        'mei_exclusion_date',
    ];

    protected $dates = [
        'simple_option_date',
        'simple_exclusion_date',
        'mei_option_date',
        'mei_exclusion_date',
    ];

    /**
     * Retorna os campos de data do modelo.
     *
     * @return array
     */
    public function getDateFields(): array
    {
        return $this->dates;
    }
}
