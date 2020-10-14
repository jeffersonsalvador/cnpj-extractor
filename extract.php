<?php
ini_set('display_errors', true);
require __DIR__ . '/vendor/autoload.php';

use App\Company;

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
        echo " -> SALVANDO CSV\n";
        $fp = fopen('csv/companies.csv', 'w');
        foreach ($this->data['companies'] as $index => $fields) {
            echo ' -> ' . round(($index / count($this->data['companies'])) * 100, 2) . "%\r";
            fputcsv($fp, $fields);
        }
        echo "\n";
        fclose($fp);
    }

    public function strToArray($record) {
        $type = substr($record, 0, 1);
        if ($type === "0") {
            $this->data['header'] = '';
        }
        if ($type === "1") {
            $this->data['companies'][] = Company::getCompany($record);
        }
    }

    public function message($message)
    {
        $size = strlen($message);
        echo "\n|" . str_pad('', $size, '=', STR_PAD_LEFT) . "=|\n";
        echo "| {$message} |\n";
        echo '|' . str_pad('', $size, '=', STR_PAD_LEFT) . "=|\n\n";
    }

}

$cnpjFull = new CNPJFull();
