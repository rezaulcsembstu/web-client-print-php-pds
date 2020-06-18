<?php

namespace Neodynamic\SDK\Web;

/**
 * It represents a file in the server that will be printed at the client side.
 */
class PrintFile
{

    public $fileIsPasswordProtected = false;
    public $fileExtension = '';

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
    /**
     * Gets or sets the Encryption Metadata.
     * @var EncryptMetadata
     */
    public $encryptMetadata = null;

    /**
     * Gets or sets whether to delete this file from the client device after printing it. Default is true.
     * @var boolean
     */
    public $deleteAfterPrinting = true;

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
        $pfc = '';
        if ($this->copies > 1) {
            $pfc = 'PFC=' . $this->copies;
        }
        $df = 'DEL=F';
        if ($this->deleteAfterPrinting) {
            $df = '';
        }

        $fn = $file;
        $ext = '';
        if (strrpos($fn, '.') > 0) {
            $fn = substr($fn, 0, strrpos($fn, '.'));
            $ext = substr($file, strrpos($file, '.'));
        }

        if (Utils::isNullOrEmptyString($this->fileExtension)) {
            $file = $fn . $pfc . $df . $ext;
        } else {
            $file = $fn . $pfc . $df . $this->fileExtension;
        }

        $fileContent = $this->getFileContent();

        if (
            $this->encryptMetadata != null &&
            Utils::isNullOrEmptyString($this->encryptMetadata->publicKeyBase64) == false &&
            $this->fileIsPasswordProtected == false
        ) {

            //validate Encrypt Metadata
            $this->encryptMetadata->validateMetadata();
            //Encrypt content
            $fileContent = SecUtils::aesEncrypt(
                $fileContent,
                $this->encryptMetadata->password,
                $this->encryptMetadata->salt,
                $this->encryptMetadata->iv,
                $this->encryptMetadata->iterations
            );
        }

        return self::PREFIX . $file . self::SEP . $fileContent;
    }

    public function getFileContent()
    {
        if (!Utils::isNullOrEmptyString($this->filePath)) {
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        } else {
            $content = $this->fileBinaryContent;
        }
        return $content;
    }
}