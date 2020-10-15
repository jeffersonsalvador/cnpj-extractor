<?php
namespace App;


class Cnae
{
    const CNPJ = 'cnpj';
    const CNAE = 'cnae';

    public static function getCnae($record)
    {
        return [
            self::CNPJ => trim(substr($record, 3, 14)),
            self::CNAE => trim(substr($record, 17, 7))
        ];
    }

}
