<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\TextAlignment;
use Neodynamic\SDK\Web\PrintOrientation;

/**
 * It represents a plain text file in the server that will be printed at the client side.
 */
class PrintFileTXT extends PrintFile
{

    /**
     * Gets or sets the Text content to be printed. Default is an empty string.
     * @var string 
     */
    public $textContent = '';

    /**
     * Gets or sets the alignment of the text content. Default is Left alignment.
     * @var integer 
     */
    public $textAlignment = TextAlignment::Left;

    /**
     * Gets or sets the font name. Default is Arial.
     * @var string 
     */
    public $fontName = 'Arial';

    /**
     * Gets or sets whether the text is bold. Default is False.
     * @var boolean 
     */
    public $fontBold = false;

    /**
     * Gets or sets whether the text has the italic style applied. Default is False.
     * @var boolean 
     */
    public $fontItalic = false;

    /**
     * Gets or sets whether the text is underlined. Default is False.
     * @var boolean 
     */
    public $fontUnderline = false;

    /**
     * Gets or sets whether the text is printed with a horizontal line through it. Default is False.
     * @var boolean
     */
    public $fontStrikeThrough = false;

    /**
     * Gets or sets the font size in Points unit. Default is 10pt. 
     * @var float 
     */
    public $fontSizeInPoints = 10.0;

    /**
     * Gets or sets the Color for the printed text. Color must be specified in Hex notation for RGB channels respectively e.g. #rgb or #rrggbb. Default is #000000.
     * @var string 
     */
    public $textColor = "#000000";

    /**
     * Gets or sets the print orientation. Default is Portrait.
     * @var integer 
     */
    public $printOrientation = PrintOrientation::Portrait;

    /**
     * Gets or sets the left margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
     * @var float 
     */
    public $marginLeft = 0.5;

    /**
     * Gets or sets the right margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
     * @var float 
     */
    public $marginRight = 0.5;

    /**
     * Gets or sets the top margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
     * @var float 
     */
    public $marginTop = 0.5;

    /**
     * Gets or sets the bottom margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
     * @var float 
     */
    public $marginBottom = 0.5;


    public function serialize()
    {
        $this->fileIsPasswordProtected = false;

        $this->fileExtension = '.wtxt';

        return parent::serialize();
    }

    public function getFileContent()
    {

        $metadata = $this->printOrientation;
        $metadata .= Utils::SER_SEP . $this->textAlignment;
        $metadata .= Utils::SER_SEP . $this->fontName;
        $metadata .= Utils::SER_SEP . strval($this->fontSizeInPoints);
        $metadata .= Utils::SER_SEP . ($this->fontBold ? '1' : '0');
        $metadata .= Utils::SER_SEP . ($this->fontItalic ? '1' : '0');
        $metadata .= Utils::SER_SEP . ($this->fontUnderline ? '1' : '0');
        $metadata .= Utils::SER_SEP . ($this->fontStrikeThrough ? '1' : '0');
        $metadata .= Utils::SER_SEP . $this->textColor;
        $metadata .= Utils::SER_SEP . strval($this->marginLeft);
        $metadata .= Utils::SER_SEP . strval($this->marginTop);
        $metadata .= Utils::SER_SEP . strval($this->marginRight);
        $metadata .= Utils::SER_SEP . strval($this->marginBottom);

        $content = $this->textContent;
        if (Utils::isNullOrEmptyString($content)) {
            if (!Utils::isNullOrEmptyString($this->filePath)) {
                $handle = fopen($this->filePath, 'rb');
                $content = fread($handle, filesize($this->filePath));
                fclose($handle);
            } else {
                $content = $this->fileBinaryContent;
            }
        }

        if (Utils::isNullOrEmptyString($content)) {
            throw new Exception('The specified Text file is empty and cannot be printed.');
        }

        return $metadata . chr(10) . $content;
    }
}