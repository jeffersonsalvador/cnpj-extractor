<?php

namespace App;

class Company
{
    use Header;

    const CNPJ = 'cnpj';
    const MATRIZ_FILIAL = 'matriz_filial';
    const RAZAO_SOCIAL = 'razao_social';
    const NOME_FANTASIA = 'nome_fantasia';
    const SITUACAO = 'situacao';
    const DATA_SITUACAO = 'data_situacao';
    const MOTIVO_SITUACAO = 'motivo_situacao';
    const NM_CIDADE_EXTERIOR = 'nm_cidade_exterior';
    const COD_PAIS = 'cod_pais';
    const NOME_PAIS = 'nome_pais';
    const COD_NAT_JURIDICA = 'cod_nat_juridica';
    const DATA_INICIO_ATIV = 'data_inicio_ativ';
    const CNAE_FISCAL = 'cnae_fiscal';
    const TIPO_LOGRADOURO = 'tipo_logradouro';
    const LOGRADOURO = 'logradouro';
    const NUMERO = 'numero';
    const COMPLEMENTO = 'complemento';
    const BAIRRO = 'bairro';
    const CEP = 'cep';
    const UF = 'uf';
    const COD_MUNICIPIO = 'cod_municipio';
    const MUNICIPIO = 'municipio';
    const TELEFONE_1 = 'telefone_1';
    const TELEFONE_2 = 'telefone_2';
    const EMAIL = 'email';
    const QUALIF_RESP = 'qualif_resp';
    const CAPITAL_SOCIAL = 'capital_social';
    const PORTE = 'porte';
    const OPC_SIMPLES = 'opc_simples';
    const DATA_OPCAO_SIMPLES = 'data_opc_simples';
    const DATA_EXC_SIMPLES = 'data_exc_simples';
    const OPC_MEI = 'opc_mei';
    const SIT_ESPECIAL = 'sit_especial';
    const DATA_SIT_ESPECIAL = 'data_sit_especial';

    private $situacao_cadastral = [
        '01' => 'NULA',
        '02' => 'ATIVA',
        '03' => 'SUSPENSA',
        '04' => 'INAPTA',
        '08' => 'BAIXADA'
    ];

    public static function getCompany($record)
    {
        return [
            self::CNPJ =>               trim(substr($record, 3, 14)),
            self::MATRIZ_FILIAL =>      trim(substr($record, 17, 1)),
            self::RAZAO_SOCIAL =>       self::removeSpecialChars(substr($record, 18, 150)),
            self::NOME_FANTASIA =>      self::removeSpecialChars(substr($record, 168, 55)),
            self::SITUACAO =>           trim(substr($record, 223, 2)),
            self::DATA_SITUACAO =>      trim(substr($record, 225, 8)),
            self::MOTIVO_SITUACAO =>    self::removeSpecialChars(substr($record, 233, 2)),
            self::NM_CIDADE_EXTERIOR => self::removeSpecialChars(substr($record, 235, 55)),
            self::COD_PAIS =>           trim(substr($record, 290, 3)),
            self::NOME_PAIS =>          self::removeSpecialChars(substr($record,  293, 70)),
            self::COD_NAT_JURIDICA =>   trim(substr($record, 363, 4)),
            self::DATA_INICIO_ATIV =>   trim(substr($record, 367, 8)),
            self::CNAE_FISCAL =>        trim(substr($record, 375,7)),
            self::TIPO_LOGRADOURO =>    self::removeSpecialChars(substr($record, 382, 20)),
            self::LOGRADOURO =>         self::removeSpecialChars(substr($record, 402, 60)),
            self::NUMERO =>             self::removeSpecialChars(substr($record, 462, 6)),
            self::COMPLEMENTO =>        self::removeSpecialChars(substr($record, 468, 156)),
            self::BAIRRO =>             self::removeSpecialChars(substr($record, 624, 50)),
            self::CEP =>                trim(substr($record, 674, 8)),
            self::UF =>                 trim(substr($record, 682, 2)),
            self::COD_MUNICIPIO =>      trim(substr($record, 684, 4)),
            self::MUNICIPIO =>          self::removeSpecialChars(substr($record, 688, 50)),
            self::TELEFONE_1 =>         trim(substr($record, 738, 12)),
            self::TELEFONE_2 =>         trim(substr($record, 750, 12)),
            self::EMAIL =>              trim(substr($record, 774, 115)),
            self::QUALIF_RESP =>        trim(substr($record, 889, 2)),
            self::CAPITAL_SOCIAL =>     trim(substr($record, 891, 14)),
            self::PORTE =>              trim(substr($record, 905, 2)),
            self::OPC_SIMPLES =>        trim(substr($record, 907, 2)),
            self::DATA_OPCAO_SIMPLES => trim(substr($record, 908, 8)),
            self::DATA_EXC_SIMPLES =>   trim(substr($record, 916, 8)),
            self::OPC_MEI =>            trim(substr($record, 924, 1)),
            self::SIT_ESPECIAL =>       self::removeSpecialChars(substr($record, 925, 23)),
            self::DATA_SIT_ESPECIAL =>  trim(substr($record, 948, 8)),
        ];
    }

    private static function removeSpecialChars($string)
    {
        return preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', trim($string));
    }

}
