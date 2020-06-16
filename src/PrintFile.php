<?php

namespace Neodynamic\SDK\Web;


/**
 * It represents a file in the server that will be printed at the client side.
 */
class PrintFile
{

    /**
     * Gets or sets the path of the file at the server side that will be printed at the client side.
     * @var string 
     */
    public $filePath = '';
    /**
     * Gets or sets the file name that will be created at the client side. 
     * It must include the file extension like .pdf, .txt, .doc, .xls, etc.
     * @var string 
     */
    public $fileName = '';
    /**
     * Gets or sets the binary content of the file at the server side that will be printed at the client side.
     * @var string 
     */
    public $fileBinaryContent = '';

    /**
     * Gets or sets the num of copies for printing this file. Default is 1.
     * @var integer
     */
    public $copies = 1;

    const PREFIX = 'wcpPF:';
    const SEP = '|';

    /**
     * 
     * @param string $filePath The path of the file at the server side that will be printed at the client side.
     * @param string $fileName The file name that will be created at the client side. It must include the file extension like .pdf, .txt, .doc, .xls, etc.
     * @param string $fileBinaryContent The binary content of the file at the server side that will be printed at the client side.
     */
    public function __construct($filePath, $fileName, $fileBinaryContent)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->fileBinaryContent = $fileBinaryContent;
    }

    public function serialize()
    {
        $file = str_replace('\\', 'BACKSLASHCHAR', $this->fileName);
        if ($this->copies > 1) {
            $pfc = 'PFC=' . $this->copies;
            $file = substr($file, 0, strrpos($file, '.')) . $pfc . substr($file, strrpos($file, '.'));
        }
        return self::PREFIX . $file . self::SEP . $this->getFileContent();
    }

    public function getFileContent()
    {
        $content = $this->fileBinaryContent;
        if (!Utils::isNullOrEmptyString($this->filePath)) {
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        }
        return $content;
    }
}