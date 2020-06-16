<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\PrintRotation;

/**
 * It represents a PDF file in the server that will be printed at the client side.
 */
class PrintFilePDF extends PrintFile
{

    /**
     * Gets or sets whether to print the PDF document with color images, texts, or other objects as shades of gray. Default is False.
     * @var boolean 
     */
    public $printAsGrayscale = false;

    /**
     * Gets or sets whether to print any annotations, if any, available in the PDF document. Default is False.
     * @var boolean 
     */
    public $printAnnotations = false;

    /**
     * Gets or sets a subset of pages to print. It can be individual page numbers, a range, or a combination. For example: 1, 5-10, 25, 50. Default is an empty string which means print all pages.
     * @var string 
     */
    public $pagesRange = '';

    /**
     * Gets or sets whether pages are printed in reverse order. Default is False.
     * @var boolean 
     */
    public $printInReverseOrder = false;

    /**
     * Gets or sets the print rotation. Default is None.
     * @var integer 
     */
    public $printRotation = PrintRotation::None;


    public function serialize()
    {
        $file = str_replace('\\', 'BACKSLASHCHAR', $this->fileName);
        if ($this->copies > 1) {
            $pfc = 'PFC=' . $this->copies;
            $file = substr($file, 0, strrpos($file, '.')) . $pfc . substr($file, strrpos($file, '.'));
        }

        return self::PREFIX . $file . '.wpdf' . self::SEP . $this->getFileContent();
    }

    public function getFileContent()
    {

        $pr = urldecode($this->pagesRange);
        if (!Utils::isNullOrEmptyString($pr)) {
            if (preg_match('/^(?!([ \d]*-){2})\d+(?: *[-,] *\d+)*$/', $pr)) {
                //validate range
                $ranges = explode(',', str_replace(' ', '', $pr)); //remove any space chars

                for ($i = 0; $i < count($ranges); $i++) {
                    if (strpos($ranges[$i], '-') > 0) {
                        $pages = explode('-', $ranges[$i]);
                        if (intval($pages[0]) > intval($pages[1])) {
                            throw new Exception("The specified PageRange is not valid.");
                        }
                    }
                }
            } else
                throw new Exception("The specified PageRange is not valid.");
        }

        $metadata = ($this->printAsGrayscale ? '1' : '0');
        $metadata .= Utils::SER_SEP . ($this->printAnnotations ? '1' : '0');
        $metadata .= Utils::SER_SEP . (Utils::isNullOrEmptyString($pr) ? 'A' : $pr);
        $metadata .= Utils::SER_SEP . ($this->printInReverseOrder ? '1' : '0');
        $metadata .= Utils::SER_SEP . $this->printRotation;

        $content = $this->fileBinaryContent;
        if (!Utils::isNullOrEmptyString($this->filePath)) {
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        }
        return $metadata . chr(10) . $content;
    }
}