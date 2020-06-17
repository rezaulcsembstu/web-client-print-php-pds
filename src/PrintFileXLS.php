<?php

namespace Neodynamic\SDK\Web;

/**
 * It represents a XLS file in the server that will be printed at the client side.
 */
class PrintFileXLS extends PrintFile
{

    /**
     * Gets or sets the number of the page at which to start printing. Default is 0 (zero) which means printing starts at the beginning.
     * @var integer 
     */
    public $pagesFrom = 0;

    /**
     * Gets or sets the number of the last page to print. Default is 0 (zero) which means printing ends with the last page.
     * @var integer 
     */
    public $pagesTo = 0;


    /**
     * Gets or sets the password for this PDF file.
     * @var string 
     */
    public $password = '';

    /**
     * Gets or sets whether to perform manual duplex printing. Default is False. Manual duplex lets you print on both sides of a sheet by ordering the print job so that after the first half of the print job has been printed, the job can be flipped over for the second side printing.
     * @var boolean 
     */
    public $duplexPrinting = false;

    /**
     * Gets or sets the dialog message to prompt to the user to flip pages after first half of print job has been printed. Default is an empty string.
     * @var string 
     */
    public $duplexPrintingDialogMessage = '';


    public function serialize()
    {
        $this->fileExtension = '.wxls';

        return parent::serialize();
    }

    public function getFileContent()
    {

        $metadata = strval($this->pagesFrom);
        $metadata .= Utils::SER_SEP . strval($this->pagesTo);
        $metadata .= Utils::SER_SEP;

        $this->fileIsPasswordProtected = !Utils::isNullOrEmptyString($this->password);

        if ($this->fileIsPasswordProtected == false) {
            $metadata .= 'N';
        } else {
            if (Utils::isNullOrEmptyString($this->encryptMetadata->publicKeyBase64) == false) {
                $metadata .= base64_encode(SecUtils::rsaVerifyAndEncrypt($this->encryptMetadata->publicKeyBase64, $this->encryptMetadata->publicKeySignatureBase64, $this->password));
            } else {
                $metadata .= base64_encode($this->password);
            }
        }

        $metadata .= Utils::SER_SEP . ($this->duplexPrinting ? '1' : '0');
        $metadata .= Utils::SER_SEP . (Utils::isNullOrEmptyString($this->duplexPrintingDialogMessage) ? 'D' : base64_encode($this->duplexPrintingDialogMessage));

        $metadataLength = strlen($metadata);
        $metadata .= Utils::SER_SEP;
        $metadataLength++;
        $metadataLength += strlen(strval($metadataLength));
        $metadata .= strval($metadataLength);

        if (!Utils::isNullOrEmptyString($this->filePath)) {
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        } else {
            $content = $this->fileBinaryContent;
        }

        return $content . $metadata;
    }
}