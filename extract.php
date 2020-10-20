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
        $this->data['empresas'][0] = Company::getHeader();
        $this->data['socios'][0] = Partner::getHeader();
        $this->data['cnae-secundarios'][0] = Cnae::getHeader();
        $this->start();
    }

    private function start()
    {
        $this->findZipFiles();
        $this->readZipFiles();
        $this->compactFiles();
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
            $this->saveCSV();
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
            $this->loading($i * self::CHUNK_SIZE, $filesize);
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
            echo "Salvando {$type} em CSV\n";
            $csvFile = "csv/{$type}.csv";
            $fp = fopen($csvFile, 'a');
            foreach ($data as $index => $fields) {
                $this->loading($index, count($data));
                fputcsv($fp, $fields);
            }
            echo "\n";
            fclose($fp);
            $this->data[$type] = [];
        }
    }

    private function compactFiles()
    {
        foreach ($this->data as $type => $data) {
            $csvFile = "csv/{$type}.csv";
            $zip = new ZipArchive();
            if ($zip->open("csv/{$type}.zip", ZipArchive::CREATE) === TRUE)
            {
                echo "Compactando {$type}\n";
                $zip->addFile($csvFile);
                $zip->close();
                if (file_exists("csv/{$type}.zip")) {
                    unlink($csvFile);
                }
            }
        }
        unset($this->data);
    }

    public function strToArray($record) {
        $type = substr($record, 0, 1);
        if ($type === "1") {
            $this->data['empresas'][] = Company::getCompany($record);
        }
        if ($type === "2") {
            $this->data['socios'][] = Partner::getPartner($record);
        }
        if ($type === "6") {
            $this->data['cnae-secundarios'][] = Cnae::getCnae($record);
        }
    }

    public function message($message)
    {
        echo "\n";
        echo "{$message} \n";
    }

    public function loading($partial, $total)
    {
        $percent = round(($partial / $total) * 100);
        $input = '';
        for ($i = 2; $i <= $percent; $i+=2) {
            $input .= '=';
        }
        echo '[' . str_pad($input, 50, '_', STR_PAD_RIGHT) . "] - {$percent}% \r";
    }

}

$cnpjFull = new CNPJFull();
