<?php

namespace Neodynamic\SDK\Web;

use Exception;

// Setting WebClientPrint
// TODO:load optimization
// WebClientPrint::$licenseOwner = '';
// WebClientPrint::$licenseKey = '';

//Set wcpcache folder RELATIVE to WebClientPrint.php file
//FILE WRITE permission on this folder is required!!!
//TODO:load optimization
//WebClientPrint::$wcpCacheFolder = 'wcpcache/';

/**
 * WebClientPrint provides functions for registering the "WebClientPrint for PHP" solution 
 * script code in PHP web pages as well as for processing client requests and managing the
 * internal cache.
 * 
 * @author Neodynamic <http://neodynamic.com/support>
 * @copyright (c) 2019, Neodynamic SRL
 * @license http://neodynamic.com/eula Neodynamic EULA
 */
class WebClientPrint
{

    const VERSION = '5.0.0.0';
    const CLIENT_PRINT_JOB = 'clientPrint';
    const WCP = 'WEB_CLIENT_PRINT';
    const WCP_SCRIPT_AXD_GET_PRINTERS = 'getPrinters';
    const WCP_SCRIPT_AXD_GET_PRINTERSINFO = 'getPrintersInfo';
    const WCPP_SET_PRINTERS = 'printers';
    const WCPP_SET_PRINTERSINFO = 'printersInfo';
    const WCP_SCRIPT_AXD_GET_WCPPVERSION = 'getWcppVersion';
    const WCPP_SET_VERSION = 'wcppVer';
    const GEN_WCP_SCRIPT_URL = 'u';
    const GEN_DETECT_WCPP_SCRIPT = 'd';
    const SID = 'sid';
    const PING = 'wcppping';

    const WCP_CACHE_WCPP_INSTALLED = 'WCPP_INSTALLED';
    const WCP_CACHE_WCPP_VER = 'WCPP_VER';
    const WCP_CACHE_PRINTERS = 'PRINTERS';
    const WCP_CACHE_PRINTERSINFO = 'PRINTERSINFO';


    /**
     * Gets or sets the License Owner
     * @var string 
     */
    static $licenseOwner = '';
    /**
     * Gets or sets the License Key
     * @var string
     */
    static $licenseKey = '';
    /**
     * Gets or sets the ABSOLUTE URL to WebClientPrint.php file
     * @var string
     */
    static $webClientPrintAbsoluteUrl = '';
    /**
     * Gets or sets the wcpcache folder URL RELATIVE to WebClientPrint.php file. 
     * FILE WRITE permission on this folder is required!!!
     * @var string
     */
    static $wcpCacheFolder = '';

    /**
     * Adds a new entry to the built-in file system cache. 
     * @param string $sid The user's session id
     * @param string $key The cache entry key
     * @param string $val The data value to put in the cache
     * @throws Exception
     */
    public static function cacheAdd($sid, $key, $val)
    {
        if (Utils::isNullOrEmptyString(self::$wcpCacheFolder)) {
            throw new Exception('WebClientPrint wcpCacheFolder is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sid)) {
            throw new Exception('WebClientPrint FileName cache is missing, please specify it.');
        }
        $cacheFileName = (Utils::strEndsWith(self::$wcpCacheFolder, '/') ? self::$wcpCacheFolder : self::$wcpCacheFolder . '/') . $sid . '.wcpcache';
        $dataWCPP_VER = '';
        $dataPRINTERS = '';
        $dataPRINTERSINFO = '';

        if (file_exists($cacheFileName)) {
            $cache_info = parse_ini_file($cacheFileName);

            $dataWCPP_VER = $cache_info[self::WCP_CACHE_WCPP_VER];
            $dataPRINTERS = $cache_info[self::WCP_CACHE_PRINTERS];
            $dataPRINTERSINFO = $cache_info[self::WCP_CACHE_PRINTERSINFO];
        }

        if ($key === self::WCP_CACHE_WCPP_VER) {
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER . '=' . '"' . $val . '"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS . '=' . '"' . $dataPRINTERS . '"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO . '=' . '"' . $dataPRINTERSINFO . '"';
        } else if ($key === self::WCP_CACHE_PRINTERS) {
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER . '=' . '"' . $dataWCPP_VER . '"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS . '=' . '"' . $val . '"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO . '=' . '"' . $dataPRINTERSINFO . '"';
        } else if ($key === self::WCP_CACHE_PRINTERSINFO) {
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER . '=' . '"' . $dataWCPP_VER . '"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS . '=' . '"' . $dataPRINTERS . '"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO . '=' . '"' . $val . '"';
        }

        $data = $dataWCPP_VER . chr(13) . chr(10) . $dataPRINTERS . chr(13) . chr(10) . $dataPRINTERSINFO;
        $handle = fopen($cacheFileName, 'w') or die('Cannot open file:  ' . $cacheFileName);
        fwrite($handle, $data);
        fclose($handle);
    }

    /**
     * Gets a value from the built-in file system cache based on the specified sid & key 
     * @param string $sid The user's session id
     * @param string $key The cache entry key
     * @return string Returns the value from the cache for the specified sid & key if it's found; or an empty string otherwise.
     * @throws Exception
     */
    public static function cacheGet($sid, $key)
    {
        if (Utils::isNullOrEmptyString(self::$wcpCacheFolder)) {
            throw new Exception('WebClientPrint wcpCacheFolder is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sid)) {
            throw new Exception('WebClientPrint FileName cache is missing, please specify it.');
        }
        $cacheFileName = (Utils::strEndsWith(self::$wcpCacheFolder, '/') ? self::$wcpCacheFolder : self::$wcpCacheFolder . '/') . $sid . '.wcpcache';
        if (file_exists($cacheFileName)) {
            $cache_info = parse_ini_file($cacheFileName, FALSE, INI_SCANNER_RAW);

            if ($key === self::WCP_CACHE_WCPP_VER || $key === self::WCP_CACHE_WCPP_INSTALLED) {
                return $cache_info[self::WCP_CACHE_WCPP_VER];
            } else if ($key === self::WCP_CACHE_PRINTERS) {
                return $cache_info[self::WCP_CACHE_PRINTERS];
            } else if ($key === self::WCP_CACHE_PRINTERSINFO) {
                return $cache_info[self::WCP_CACHE_PRINTERSINFO];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * Cleans the built-in file system cache
     * @param integer $minutes The number of minutes after any files on the cache will be removed.
     */
    public static function cacheClean($minutes)
    {
        if (!Utils::isNullOrEmptyString(self::$wcpCacheFolder)) {
            $cacheDir = (Utils::strEndsWith(self::$wcpCacheFolder, '/') ? self::$wcpCacheFolder : self::$wcpCacheFolder . '/');
            if ($handle = opendir($cacheDir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && (time() - filectime($cacheDir . $file)) > (60 * $minutes)) {
                        unlink($cacheDir . $file);
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Returns script code for detecting whether WCPP is installed at the client machine.
     *
     * The WCPP-detection script code ends with a 'success' or 'failure' status.
     * You can handle both situation by creating two javascript functions which names 
     * must be wcppDetectOnSuccess() and wcppDetectOnFailure(). 
     * These two functions will be automatically invoked by the WCPP-detection script code.
     * 
     * The WCPP-detection script uses a delay time variable which by default is 10000 ms (10 sec). 
     * You can change it by creating a javascript global variable which name must be wcppPingDelay_ms. 
     * For example, to use 5 sec instead of 10, you should add this to your script: 
     *   
     * var wcppPingDelay_ms = 5000;
     *    
     * @param string $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param string $sessionID The current Session ID.
     * @return string A [script] tag linking to the WCPP-detection script code.
     * @throws Exception
     */
    public static function createWcppDetectionScript($webClientPrintControllerAbsoluteUrl, $sessionID)
    {

        if (
            Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) ||
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')
        ) {
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sessionID)) {
            throw new Exception('Session ID is missing, please specify it.');
        }

        $url = $webClientPrintControllerAbsoluteUrl . '?' . self::GEN_DETECT_WCPP_SCRIPT . '=' . $sessionID;
        return '<script src="' . $url . '" type="text/javascript"></script>';
    }


    /**
     * Returns a [script] tag linking to the WebClientPrint script code by using 
     * the specified URL for the client print job generation.
     * 
     * @param string $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param string $clientPrintJobAbsoluteUrl The Absolute URL to the PHP file that creates ClientPrintJob objects.
     * @paran string $sessionID The current Session ID.
     * @return string A [script] tag linking to the WebClientPrint script code by using the specified URL for the client print job generation.
     * @throws Exception
     */
    public static function createScript($webClientPrintControllerAbsoluteUrl, $clientPrintJobAbsoluteUrl, $sessionID)
    {
        if (
            Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) ||
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')
        ) {
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }
        if (
            Utils::isNullOrEmptyString($clientPrintJobAbsoluteUrl) ||
            !Utils::strStartsWith($clientPrintJobAbsoluteUrl, 'http')
        ) {
            throw new Exception('ClientPrintJob absolute URL is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sessionID)) {
            throw new Exception('Session ID is missing, please specify it.');
        }


        $wcpHandler = $webClientPrintControllerAbsoluteUrl . '?';
        $wcpHandler .= self::VERSION;
        $wcpHandler .= '&';
        $wcpHandler .= microtime(true);
        $wcpHandler .= '&sid=';
        $wcpHandler .= $sessionID;
        $wcpHandler .= '&' . self::GEN_WCP_SCRIPT_URL . '=';
        $wcpHandler .= base64_encode($clientPrintJobAbsoluteUrl);
        return '<script src="' . $wcpHandler . '" type="text/javascript"></script>';
    }


    /**
     * Generates the WebClientPrint scripts based on the specified query string. Result is stored in the HTTP Response Content
     * 
     * @param type $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param type $queryString The Query String from current HTTP Request.
     */
    public static function generateScript($webClientPrintControllerAbsoluteUrl, $queryString)
    {
        if (
            Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) ||
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')
        ) {
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }

        parse_str($queryString, $qs);

        if (isset($qs[self::GEN_DETECT_WCPP_SCRIPT])) {

            $curSID = $qs[self::GEN_DETECT_WCPP_SCRIPT];
            $dynamicIframeId = 'i' . substr(uniqid(), 0, 3);
            $absoluteWcpAxd = $webClientPrintControllerAbsoluteUrl . '?' . self::SID . '=' . $curSID;

            $s1 = 'dmFyIGpzV0NQUD0oZnVuY3Rpb24oKXt2YXIgc2V0PDw8LU5FTy1IVE1MLUlELT4+Pj1mdW5jdGlvbigpe2lmKHdpbmRvdy5jaHJvbWUpeyQoJyM8PDwtTkVPLUhUTUwtSUQtPj4+JykuYXR0cignaHJlZicsJ3dlYmNsaWVudHByaW50djonK2FyZ3VtZW50c1swXSk7dmFyIGE9JCgnYSM8PDwtTkVPLUhUTUwtSUQtPj4+JylbMF07dmFyIGV2T2JqPWRvY3VtZW50LmNyZWF0ZUV2ZW50KCdNb3VzZUV2ZW50cycpO2V2T2JqLmluaXRFdmVudCgnY2xpY2snLHRydWUsdHJ1ZSk7YS5kaXNwYXRjaEV2ZW50KGV2T2JqKX1lbHNleyQoJyM8PDwtTkVPLUhUTUwtSUQtPj4+JykuYXR0cignc3JjJywnd2ViY2xpZW50cHJpbnR2OicrYXJndW1lbnRzWzBdKX19O3JldHVybntpbml0OmZ1bmN0aW9uKCl7aWYod2luZG93LmNocm9tZSl7JCgnPGEgLz4nLHtpZDonPDw8LU5FTy1IVE1MLUlELT4+Pid9KS5hcHBlbmRUbygnYm9keScpfWVsc2V7JCgnPGlmcmFtZSAvPicse25hbWU6Jzw8PC1ORU8tSFRNTC1JRC0+Pj4nLGlkOic8PDwtTkVPLUhUTUwtSUQtPj4+Jyx3aWR0aDonMScsaGVpZ2h0OicxJyxzdHlsZTondmlzaWJpbGl0eTpoaWRkZW47cG9zaXRpb246YWJzb2x1dGUnfSkuYXBwZW5kVG8oJ2JvZHknKX19LHBpbmc6ZnVuY3Rpb24oKXtzZXQ8PDwtTkVPLUhUTUwtSUQtPj4+KCc8PDwtTkVPLVBJTkctVVJMLT4+PicrKGFyZ3VtZW50cy5sZW5ndGg9PTE/JyYnK2FyZ3VtZW50c1swXTonJykpO3ZhciBkZWxheV9tcz0odHlwZW9mIHdjcHBQaW5nRGVsYXlfbXM9PT0ndW5kZWZpbmVkJyk/MDp3Y3BwUGluZ0RlbGF5X21zO2lmKGRlbGF5X21zPjApe3NldFRpbWVvdXQoZnVuY3Rpb24oKXskLmdldCgnPDw8LU5FTy1VU0VSLUhBUy1XQ1BQLT4+PicsZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7d2NwcERldGVjdE9uU3VjY2VzcyhkYXRhKX1lbHNle3djcHBEZXRlY3RPbkZhaWx1cmUoKX19KX0sZGVsYXlfbXMpfWVsc2V7dmFyIGZuY1dDUFA9c2V0SW50ZXJ2YWwoZ2V0V0NQUFZlcix3Y3BwUGluZ1RpbWVvdXRTdGVwX21zKTt2YXIgd2NwcF9jb3VudD0wO2Z1bmN0aW9uIGdldFdDUFBWZXIoKXtpZih3Y3BwX2NvdW50PD13Y3BwUGluZ1RpbWVvdXRfbXMpeyQuZ2V0KCc8PDwtTkVPLVVTRVItSEFTLVdDUFAtPj4+Jyx7J18nOiQubm93KCl9LGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe2NsZWFySW50ZXJ2YWwoZm5jV0NQUCk7d2NwcERldGVjdE9uU3VjY2VzcyhkYXRhKX19KTt3Y3BwX2NvdW50Kz13Y3BwUGluZ1RpbWVvdXRTdGVwX21zfWVsc2V7Y2xlYXJJbnRlcnZhbChmbmNXQ1BQKTt3Y3BwRGV0ZWN0T25GYWlsdXJlKCl9fX19fX0pKCk7JChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKXtqc1dDUFAuaW5pdCgpO2pzV0NQUC5waW5nKCl9KTs=';

            $s2 = base64_decode($s1);
            $s2 = str_replace('<<<-NEO-HTML-ID->>>', $dynamicIframeId, $s2);
            $s2 = str_replace('<<<-NEO-PING-URL->>>', $absoluteWcpAxd . '&' . self::PING, $s2);
            $s2 = str_replace('<<<-NEO-USER-HAS-WCPP->>>', $absoluteWcpAxd, $s2);

            return $s2;
        } else if (isset($qs[self::GEN_WCP_SCRIPT_URL])) {

            $clientPrintJobUrl = base64_decode($qs[self::GEN_WCP_SCRIPT_URL]);
            if (strpos($clientPrintJobUrl, '?') > 0) {
                $clientPrintJobUrl .= '&';
            } else {
                $clientPrintJobUrl .= '?';
            }
            $clientPrintJobUrl .= self::CLIENT_PRINT_JOB;
            $absoluteWcpAxd = $webClientPrintControllerAbsoluteUrl;
            $wcppGetPrintersParam = '-getPrinters:' . $absoluteWcpAxd . '?' . self::WCP . '&' . self::SID . '=';
            $wcpHandlerGetPrinters = $absoluteWcpAxd . '?' . self::WCP . '&' . self::WCP_SCRIPT_AXD_GET_PRINTERS . '&' . self::SID . '=';
            $wcppGetPrintersInfoParam = '-getPrintersInfo:' . $absoluteWcpAxd . '?' . self::WCP . '&' . self::SID . '=';
            $wcpHandlerGetPrintersInfo = $absoluteWcpAxd . '?' . self::WCP . '&' . self::WCP_SCRIPT_AXD_GET_PRINTERSINFO . '&' . self::SID . '=';
            $wcppGetWcppVerParam = '-getWcppVersion:' . $absoluteWcpAxd . '?' . self::WCP . '&' . self::SID . '=';
            $wcpHandlerGetWcppVer = $absoluteWcpAxd . '?' . self::WCP . '&' . self::WCP_SCRIPT_AXD_GET_WCPPVERSION . '&' . self::SID . '=';
            $sessionIDVal = $qs[self::SID];

            $s1 = 'dmFyIGpzV2ViQ2xpZW50UHJpbnQ9KGZ1bmN0aW9uKCl7dmFyIHNldEE9ZnVuY3Rpb24oKXt2YXIgZV9pZD0naWRfJytuZXcgRGF0ZSgpLmdldFRpbWUoKTtpZih3aW5kb3cuY2hyb21lKXskKCdib2R5JykuYXBwZW5kKCc8YSBpZD1cIicrZV9pZCsnXCI+PC9hPicpOyQoJyMnK2VfaWQpLmF0dHIoJ2hyZWYnLCd3ZWJjbGllbnRwcmludHY6Jythcmd1bWVudHNbMF0pO3ZhciBhPSQoJ2EjJytlX2lkKVswXTt2YXIgZXZPYmo9ZG9jdW1lbnQuY3JlYXRlRXZlbnQoJ01vdXNlRXZlbnRzJyk7ZXZPYmouaW5pdEV2ZW50KCdjbGljaycsdHJ1ZSx0cnVlKTthLmRpc3BhdGNoRXZlbnQoZXZPYmopfWVsc2V7JCgnYm9keScpLmFwcGVuZCgnPGlmcmFtZSBuYW1lPVwiJytlX2lkKydcIiBpZD1cIicrZV9pZCsnXCIgd2lkdGg9XCIxXCIgaGVpZ2h0PVwiMVwiIHN0eWxlPVwidmlzaWJpbGl0eTpoaWRkZW47cG9zaXRpb246YWJzb2x1dGVcIiAvPicpOyQoJyMnK2VfaWQpLmF0dHIoJ3NyYycsJ3dlYmNsaWVudHByaW50djonK2FyZ3VtZW50c1swXSl9c2V0VGltZW91dChmdW5jdGlvbigpeyQoJyMnK2VfaWQpLnJlbW92ZSgpfSw1MDAwKX07cmV0dXJue3ByaW50OmZ1bmN0aW9uKCl7c2V0QSgnVVJMX1BSSU5UX0pPQicrKGFyZ3VtZW50cy5sZW5ndGg9PTE/JyYnK2FyZ3VtZW50c1swXTonJykpfSxnZXRQcmludGVyczpmdW5jdGlvbigpe3NldEEoJ1VSTF9XQ1BfQVhEX1dJVEhfR0VUX1BSSU5URVJTX0NPTU1BTkQnKyc8PDwtTkVPLVNFU1NJT04tSUQtPj4+Jyk7dmFyIGRlbGF5X21zPSh0eXBlb2Ygd2NwcEdldFByaW50ZXJzRGVsYXlfbXM9PT0ndW5kZWZpbmVkJyk/MDp3Y3BwR2V0UHJpbnRlcnNEZWxheV9tcztpZihkZWxheV9tcz4wKXtzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JC5nZXQoJ1VSTF9XQ1BfQVhEX0dFVF9QUklOVEVSUycrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nLGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe3djcEdldFByaW50ZXJzT25TdWNjZXNzKGRhdGEpfWVsc2V7d2NwR2V0UHJpbnRlcnNPbkZhaWx1cmUoKX19KX0sZGVsYXlfbXMpfWVsc2V7dmFyIGZuY0dldFByaW50ZXJzPXNldEludGVydmFsKGdldENsaWVudFByaW50ZXJzLHdjcHBHZXRQcmludGVyc1RpbWVvdXRTdGVwX21zKTt2YXIgd2NwcF9jb3VudD0wO2Z1bmN0aW9uIGdldENsaWVudFByaW50ZXJzKCl7aWYod2NwcF9jb3VudDw9d2NwcEdldFByaW50ZXJzVGltZW91dF9tcyl7JC5nZXQoJ1VSTF9XQ1BfQVhEX0dFVF9QUklOVEVSUycrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nLHsnXyc6JC5ub3coKX0sZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7Y2xlYXJJbnRlcnZhbChmbmNHZXRQcmludGVycyk7d2NwR2V0UHJpbnRlcnNPblN1Y2Nlc3MoZGF0YSl9fSk7d2NwcF9jb3VudCs9d2NwcEdldFByaW50ZXJzVGltZW91dFN0ZXBfbXN9ZWxzZXtjbGVhckludGVydmFsKGZuY0dldFByaW50ZXJzKTt3Y3BHZXRQcmludGVyc09uRmFpbHVyZSgpfX19fSxnZXRQcmludGVyc0luZm86ZnVuY3Rpb24oKXtzZXRBKCdVUkxfV0NQX0FYRF9XSVRIX0dFVF9QUklOVEVSU0lORk9fQ09NTUFORCcrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nKTt2YXIgZGVsYXlfbXM9KHR5cGVvZiB3Y3BwR2V0UHJpbnRlcnNEZWxheV9tcz09PSd1bmRlZmluZWQnKT8wOndjcHBHZXRQcmludGVyc0RlbGF5X21zO2lmKGRlbGF5X21zPjApe3NldFRpbWVvdXQoZnVuY3Rpb24oKXskLmdldCgnVVJMX1dDUF9BWERfR0VUX1BSSU5URVJTSU5GTycrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nLGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe3djcEdldFByaW50ZXJzT25TdWNjZXNzKGRhdGEpfWVsc2V7d2NwR2V0UHJpbnRlcnNPbkZhaWx1cmUoKX19KX0sZGVsYXlfbXMpfWVsc2V7dmFyIGZuY0dldFByaW50ZXJzSW5mbz1zZXRJbnRlcnZhbChnZXRDbGllbnRQcmludGVyc0luZm8sd2NwcEdldFByaW50ZXJzVGltZW91dFN0ZXBfbXMpO3ZhciB3Y3BwX2NvdW50PTA7ZnVuY3Rpb24gZ2V0Q2xpZW50UHJpbnRlcnNJbmZvKCl7aWYod2NwcF9jb3VudDw9d2NwcEdldFByaW50ZXJzVGltZW91dF9tcyl7JC5nZXQoJ1VSTF9XQ1BfQVhEX0dFVF9QUklOVEVSU0lORk8nKyc8PDwtTkVPLVNFU1NJT04tSUQtPj4+Jyx7J18nOiQubm93KCl9LGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe2NsZWFySW50ZXJ2YWwoZm5jR2V0UHJpbnRlcnNJbmZvKTt3Y3BHZXRQcmludGVyc09uU3VjY2VzcyhkYXRhKX19KTt3Y3BwX2NvdW50Kz13Y3BwR2V0UHJpbnRlcnNUaW1lb3V0U3RlcF9tc31lbHNle2NsZWFySW50ZXJ2YWwoZm5jR2V0UHJpbnRlcnNJbmZvKTt3Y3BHZXRQcmludGVyc09uRmFpbHVyZSgpfX19fSxnZXRXY3BwVmVyOmZ1bmN0aW9uKCl7c2V0QSgnVVJMX1dDUF9BWERfV0lUSF9HRVRfV0NQUFZFUlNJT05fQ09NTUFORCcrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nKTt2YXIgZGVsYXlfbXM9KHR5cGVvZiB3Y3BwR2V0VmVyRGVsYXlfbXM9PT0ndW5kZWZpbmVkJyk/MDp3Y3BwR2V0VmVyRGVsYXlfbXM7aWYoZGVsYXlfbXM+MCl7c2V0VGltZW91dChmdW5jdGlvbigpeyQuZ2V0KCdVUkxfV0NQX0FYRF9HRVRfV0NQUFZFUlNJT04nKyc8PDwtTkVPLVNFU1NJT04tSUQtPj4+JyxmdW5jdGlvbihkYXRhKXtpZihkYXRhLmxlbmd0aD4wKXt3Y3BHZXRXY3BwVmVyT25TdWNjZXNzKGRhdGEpfWVsc2V7d2NwR2V0V2NwcFZlck9uRmFpbHVyZSgpfX0pfSxkZWxheV9tcyl9ZWxzZXt2YXIgZm5jV0NQUD1zZXRJbnRlcnZhbChnZXRDbGllbnRWZXIsd2NwcEdldFZlclRpbWVvdXRTdGVwX21zKTt2YXIgd2NwcF9jb3VudD0wO2Z1bmN0aW9uIGdldENsaWVudFZlcigpe2lmKHdjcHBfY291bnQ8PXdjcHBHZXRWZXJUaW1lb3V0X21zKXskLmdldCgnVVJMX1dDUF9BWERfR0VUX1dDUFBWRVJTSU9OJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicseydfJzokLm5vdygpfSxmdW5jdGlvbihkYXRhKXtpZihkYXRhLmxlbmd0aD4wKXtjbGVhckludGVydmFsKGZuY1dDUFApO3djcEdldFdjcHBWZXJPblN1Y2Nlc3MoZGF0YSl9fSk7d2NwcF9jb3VudCs9d2NwcEdldFZlclRpbWVvdXRTdGVwX21zfWVsc2V7Y2xlYXJJbnRlcnZhbChmbmNXQ1BQKTt3Y3BHZXRXY3BwVmVyT25GYWlsdXJlKCl9fX19LHNlbmQ6ZnVuY3Rpb24oKXtzZXRBLmFwcGx5KHRoaXMsYXJndW1lbnRzKX19fSkoKTs=';

            $s2 = base64_decode($s1);
            $s2 = str_replace('URL_PRINT_JOB', $clientPrintJobUrl, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_PRINTERSINFO_COMMAND', $wcppGetPrintersInfoParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_PRINTERSINFO', $wcpHandlerGetPrintersInfo, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_PRINTERS_COMMAND', $wcppGetPrintersParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_PRINTERS', $wcpHandlerGetPrinters, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_WCPPVERSION_COMMAND', $wcppGetWcppVerParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_WCPPVERSION', $wcpHandlerGetWcppVer, $s2);
            $s2 = str_replace('<<<-NEO-SESSION-ID->>>', $sessionIDVal, $s2);

            return $s2;
        }
    }


    /**
     * Generates printing script.
     */
    const GenPrintScript = 0;
    /**
     * Generates WebClientPrint Processor (WCPP) detection script.
     */
    const GenWcppDetectScript = 1;
    /**
     * Sets the installed printers list in the website cache.
     */
    const ClientSetInstalledPrinters = 2;
    /**
     * Gets the installed printers list from the website cache.
     */
    const ClientGetInstalledPrinters = 3;
    /**
     * Sets the WebClientPrint Processor (WCPP) Version in the website cache.
     */
    const ClientSetWcppVersion = 4;
    /**
     * Gets the WebClientPrint Processor (WCPP) Version from the website cache.
     */
    const ClientGetWcppVersion = 5;
    /**
     * Sets the installed printers list with detailed info in the website cache.
     */
    const ClientSetInstalledPrintersInfo = 6;
    /**
     * Gets the installed printers list with detailed info from the website cache.
     */
    const ClientGetInstalledPrintersInfo = 7;


    /**
     * Determines the type of process request based on the Query String value. 
     * 
     * @param string $queryString The query string of the current request.
     * @return integer A valid type of process request. In case of an invalid value, an Exception is thrown.
     * @throws Exception 
     */
    public static function GetProcessRequestType($queryString)
    {
        parse_str($queryString, $qs);

        if (isset($qs[self::SID])) {
            if (isset($qs[self::PING])) {
                return self::ClientSetWcppVersion;
            } else if (isset($qs[self::WCPP_SET_VERSION])) {
                return self::ClientSetWcppVersion;
            } else if (isset($qs[self::WCPP_SET_PRINTERS])) {
                return self::ClientSetInstalledPrinters;
            } else if (isset($qs[self::WCPP_SET_PRINTERSINFO])) {
                return self::ClientSetInstalledPrintersInfo;
            } else if (isset($qs[self::WCP_SCRIPT_AXD_GET_WCPPVERSION])) {
                return self::ClientGetWcppVersion;
            } else if (isset($qs[self::WCP_SCRIPT_AXD_GET_PRINTERS])) {
                return self::ClientGetInstalledPrinters;
            } else if (isset($qs[self::WCP_SCRIPT_AXD_GET_PRINTERSINFO])) {
                return self::ClientGetInstalledPrintersInfo;
            } else if (isset($qs[self::GEN_WCP_SCRIPT_URL])) {
                return self::GenPrintScript;
            } else {
                return self::ClientGetWcppVersion;
            }
        } else if (isset($qs[self::GEN_DETECT_WCPP_SCRIPT])) {
            return self::GenWcppDetectScript;
        } else {
            throw new Exception('No valid ProcessRequestType was found in the specified QueryString.');
        }
    }
}