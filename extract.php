<?php
ini_set('display_errors', true);
require __DIR__ . '/vendor/autoload.php';

use App\Company;
use App\Partner;
use App\Cnae;

class CNPJFull
{
    const CHUNK_SIZE = 1201;
    protected $zipFiles;
    protected $data;

    function __construct()
    {
        $this->start();
    }

    private function start()
    {
        $this->findZipFiles();
        $this->readZipFiles();
        $this->saveCSV();
    }

    private function findZipFiles()
    {
        $zipFiles = preg_grep('/^([^.])/', scandir('zip'));
        if (!$zipFiles) {
            $this->message('Arquivo(s) zip não encontrado(s).');
            die();
        }
        if ($zipFiles) {
            foreach ($zipFiles as $zipFile) {
                $zip = zip_open('zip/' . $zipFile);
                if ($zip) {
                    while ($zip_entry = zip_read($zip)) {
                        $this->zipFiles []= [
                            'zip' => $zipFile,
                            'file'=> zip_entry_name($zip_entry),
                            'size' => zip_entry_filesize($zip_entry)
                        ];
                    }
                    zip_close($zip);
                }
            }
        }
    }

    private function readZipFiles()
    {
        foreach ($this->zipFiles as $zipFile)
        {
            $zipAndFileName = "zip://zip/{$zipFile['zip']}#{$zipFile['file']}";
            $this->message("Lendo arquivo: {$zipFile['zip']}");
            $this->readFileChunked($zipAndFileName, true, $zipFile['size']);
        }
    }

    public function readFileChunked($filename, $retbytes = true, $filesize = 0) {
        $buffer = '';
        $cnt    = 0;
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        $i = 1;
        while (!feof($handle)) {
            $buffer = fread($handle, self::CHUNK_SIZE);
            $this->strToArray($buffer);
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
            echo ' -> ' . round((($i * self::CHUNK_SIZE) / $filesize) * 100, 2) . "%\r";
            $i++;
        }
        echo "\n";
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt;
        }
        return $status;
    }

    private function saveCSV()
    {
        foreach ($this->data as $type => $data) {
            echo "\n -> SALVANDO CSV ($type)\n";
            $csvFile = "csv/{$type}.csv";
            $fp = fopen($csvFile, 'w');
            foreach ($data as $index => $fields) {
                echo ' -> ' . round(($index / count($data)) * 100, 2) . "%\r";
                fputcsv($fp, $fields);
            }
            echo "\n";
            fclose($fp);
            $zip = new ZipArchive();
            if ($zip->open("csv{$type}.zip", ZipArchive::CREATE) === TRUE)
            {
                echo "-> COMPACTANDO CSV ($type)\n";
                $zip->addFile($csvFile);
                $zip->close();
                if (file_exists("{$type}.zip")) {
                    unlink($csvFile);
                }
            }
        }
    }

    public function strToArray($record) {
        $type = substr($record, 0, 1);
        if ($type === "1") {
            $this->data['companies'][] = Company::getCompany($record);
        }
        if ($type === "2") {
            $this->data['partners'][] = Partner::getPartner($record);
        }
        if ($type === "6") {
            $this->data['cnaes'][] = Cnae::getCnae($record);
        }
    }

    public function message($message)
    {
        $size = strlen($message);
        echo "\n";
        echo "{$message} \n";
        echo '|' . str_pad('', $size, '=', STR_PAD_LEFT) . "=|\n";
    }

}

$cnpjFull = new CNPJFull();
