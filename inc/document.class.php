<?php
/**
 * @package     encryptfile
 * @author      Charlene Auger
 * @copyright   Copyright (c) 2015-2023 FactorFX
 * @license     AGPL License 3.0 or (at your option) any later version
 *              http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link        https://www.factorfx.com
 * @since       2023
 *
 * --------------------------------------------------------------------------
 */

 class PluginEncryptfileDocument extends Document {
        
    /**
     * sendEncrypt
     *
     * @param  mixed $doc
     * @param  mixed $context
     * @return void
     */
    function sendEncrypt($doc, $context = null) {
        $file = GLPI_DOC_DIR."/".$doc->fields['filepath'];

        $file = PluginEncryptfileEncrypt::beforeDownloadDocument($doc->fields["id"], $file, $doc->fields["filename"]);
  
        if ($context !== null) {
           $file = self::getImage($file, $context);
        }

        PluginEncryptfileDocument::sendFile($file, $doc->fields['filename'], $doc->fields['mime']);
    }
    
    /**
     * sendFile
     *
     * @param  mixed $file
     * @param  mixed $filename
     * @param  mixed $mime
     * @param  mixed $expires_headers
     * @return void
     */
    static function sendFile($file, $filename, $mime = null, $expires_headers = false) {
        // Test securite : document in DOC_DIR
        $tmpfile = str_replace(GLPI_DOC_DIR, "", $file);

        if (strstr($tmpfile, "../") || strstr($tmpfile, "..\\")) {
            Event::log($file, "sendFile", 1, "security", $_SESSION["glpiname"]." try to get a non standard file.");
            echo "Security attack!!!";
            die(1);
        }

        if (!file_exists($file)) {
            echo "Error file $file does not exist";
            die(1);
        }

        // if $mime is defined, ignore mime type by extension
        if ($mime === null && preg_match('/\.(...)$/', $file)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
        }

        // don't download picture files, see them inline
        $attachment = "";

        // if not begin 'image/'
        if (strncmp($mime, 'image/', 6) !== 0 && $mime != 'application/pdf' || $mime == 'image/svg+xml') {
            $attachment = ' attachment;';
        }

        $etag = md5_file($file);
        $lastModified = filemtime($file);

        // Make sure there is nothing in the output buffer (In case stuff was added by core or misbehaving plugin).
        // If there is any extra data, the sent file will be corrupted.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        // Now send the file with header() magic
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
        header("Etag: $etag");
        header_remove('Pragma');
        header('Cache-Control: private');

        if ($expires_headers) {
            $max_age = WEEK_TIMESTAMP;
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $max_age));
        }

        header(
            "Content-disposition:$attachment filename=\"" .
            addslashes(utf8_decode($filename)) .
            "\"; filename*=utf-8''" .
            rawurlencode($filename)
        );
        header("Content-type: ".$mime);

        // HTTP_IF_NONE_MATCH takes precedence over HTTP_IF_MODIFIED_SINCE
        // http://tools.ietf.org/html/rfc7232#section-3.3
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304); //304 - Not Modified
            exit;
        }
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
            http_response_code(304); //304 - Not Modified
            exit;
        }

        if(readfile($file)) {
            if(strpos($file, GLPI_TMP_DIR) !== false) unlink($file);
        } else {
            die ("Error opening file $file");
        }
    }
 }