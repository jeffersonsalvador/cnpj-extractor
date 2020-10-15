<?php

namespace App;

class Partner
{
    const CNPJ = 'cnpj';
    const TIPO_SOCIO = 'tipo_socio';
    const NOME_SOCIO = 'nome_socio';
    const CNPJ_CPF_SOCIO = 'cnpj_cpf_socio';
    const COD_QUALIFICACAO = 'cod_qualificacao';
    const PERC_CAPITAL = 'perc_capital';
    const DATA_ENTRADA = 'data_entrada';
    const COD_PAIS_EXT = 'cod_pais_ext';
    const NOME_PAIS_EXT = 'nome_pais_ext';
    const CPF_REPRES = 'cpf_repres';
    const NOME_REPRES = 'nome_repres';
    const COD_QUALIF_REPRES = 'cod_qualif_repres';

    public static function getPartner($record)
    {
        return [
            self::CNPJ => trim(substr($record, 3, 14)),
            self::TIPO_SOCIO => trim(substr($record, 17, 1)),
            self::NOME_SOCIO => trim(substr($record, 18, 150)),
            self::CNPJ_CPF_SOCIO => trim(substr($record, 168, 14)),
            self::COD_QUALIFICACAO => trim(substr($record, 182, 2)),
            self::PERC_CAPITAL => trim(substr($record, 184, 5)),
            self::DATA_ENTRADA => trim(substr($record, 189, 8)),
            self::COD_PAIS_EXT => trim(substr($record, 197, 3)),
            self::NOME_PAIS_EXT => trim(substr($record, 200, 70)),
            self::CPF_REPRES => trim(substr($record, 270, 11)),
            self::NOME_REPRES => trim(substr($record, 281, 60)),
            self::COD_QUALIF_REPRES => trim(substr($record, 341, 2)),
        ];
    }

}
