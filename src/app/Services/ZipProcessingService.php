<?php
/**
 * @author    Jefferson Costa
 * @copyright 2024, Málaga
 * @package   cnpj-extractor
 *
 * Created using PhpStorm at 20/01/2024 06:48
 */

namespace App\Services;

use Exception;
use ZipArchive;

/**
 * Class ZipProcessingService
 * @package App\Services
 */
class ZipProcessingService
{
    /**
     * @param $zipFilePath
     * @return string
     * @throws Exception
     */
    public function extract($zipFilePath): string
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath) === true) {
            $tempDir = sys_get_temp_dir();
            $zip->extractTo($tempDir);
            $zip->close();

            return $tempDir;
        }

        throw new Exception("Não foi possível abrir o arquivo: $zipFilePath");
    }
}
