<?php
/**
 * Created by PhpStorm.
 * User: jefferson
 * Date: 07/12/23
 * Time: 00:15
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerQualification extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'name'
    ];
}
