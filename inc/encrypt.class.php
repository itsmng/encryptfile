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

class PluginEncryptfileEncrypt extends CommonDBTM {
    static $rightname = 'plugin_encryptfile_encrypt';
    
    /**
     * getTypeName
     *
     * @param  mixed $nb
     * @return void
     */
    public static function getTypeName($nb = 1) {
        return _n('Encrypted file', 'Encrypted files', $nb, 'encryptfile');
    }
    
    /**
     * generateKey
     *
     * @return void
     */
    public function generateKey() {
        return base64_encode(sodium_crypto_secretstream_xchacha20poly1305_keygen());
    }
    
    /**
     * decryptkey
     *
     * @param  mixed $key
     * @return void
     */
    static function decryptkey($secretKey) {
        return base64_decode($secretKey);
    }
    
    /**
     * encryptFile
     *
     * @param  mixed $secretKey
     * @param  mixed $filename
     * @return void
     */
    static private function encryptFile($secretKey, $filename) {
        $inputFile = GLPI_TMP_DIR.'/'.$filename;
        $encryptedFile = GLPI_TMP_DIR.'/'.$filename.'.enc';
        $chunkSize = 4096;

        $fdIn = fopen($inputFile, 'rb');
        $fdOut = fopen($encryptedFile, 'wb');

        [$stream, $header] = sodium_crypto_secretstream_xchacha20poly1305_init_push($secretKey);

        fwrite($fdOut, $header);

        $tag = SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_MESSAGE;
        do {
            $chunk = fread($fdIn, $chunkSize);

            if (feof($fdIn)) {
                $tag = SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL;
            }

            $encryptedChunk = sodium_crypto_secretstream_xchacha20poly1305_push($stream, $chunk, '', $tag);
            fwrite($fdOut, $encryptedChunk);
        } while ($tag !== SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL);

        fclose($fdOut);
        fclose($fdIn);

        if($chunk) {
            rename($encryptedFile, $inputFile);
            return true;
        }
    }
    
    /**
     * decryptFile
     *
     * @param  mixed $secretKey
     * @param  mixed $filepath
     * @return void
     */
    static private function decryptFile($secretKey, $filepath, $filename) {
        $encryptedFile = $filepath;
        $decryptedFile = GLPI_TMP_DIR.'/'.$filename;
        $chunkSize = 4096;

        $fdIn = fopen($encryptedFile, 'rb');
        $fdOut = fopen($decryptedFile, 'wb');

        $header = fread($fdIn, SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_HEADERBYTES);

        $stream = sodium_crypto_secretstream_xchacha20poly1305_init_pull($header, $secretKey);

        do {
            $chunk = fread($fdIn, $chunkSize + SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_ABYTES);
            [$decryptedChunk, $tag] = sodium_crypto_secretstream_xchacha20poly1305_pull($stream, $chunk);

            fwrite($fdOut, $decryptedChunk);
        } while ((!feof($fdIn) && $tag !== SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL) || !$chunk);

        $ok = feof($fdIn);

        fclose($fdOut);
        fclose($fdIn);

        if (!$ok) {
            die('Invalid/corrupted input');
        } else {
            return $decryptedFile;
        }
    }
    
    /**
     * beforeAddDocument
     *
     * @param  mixed $post
     * @return void
     */
    static function beforeAddDocument(Document $post) {
        if(Session::haveRight("plugin_encryptfile_encrypt", UPDATE)) {
            $PluginEncryptfileConfig = new PluginEncryptfileConfig();
            $secretKey = $PluginEncryptfileConfig->getSecretKey($_SESSION["glpiactiveprofile"]["id"]);

            if(!is_null($secretKey)) {
                PluginEncryptfileEncrypt::encryptFile(PluginEncryptfileEncrypt::decryptkey($secretKey), $post->input["_filename"][0]);
            }
        }
    }
    
    /**
     * afterAddDocument
     *
     * @param  mixed $post
     * @return void
     */
    static function afterAddDocument(Document $post) {
        if(Session::haveRight("plugin_encryptfile_encrypt", UPDATE)) {
            $PluginEncryptfileConfig = new PluginEncryptfileConfig();
            $secretKeyId = $PluginEncryptfileConfig->getSecretKeyId($_SESSION["glpiactiveprofile"]["id"]);

            if(!is_null($secretKeyId)) {
                $PluginEncryptfileConfig->saveDocumentInfo($secretKeyId, $post->fields["id"]);
            }
        }
    }
    
    /**
     * beforeDownloadDocument
     *
     * @param  mixed $documentId
     * @param  mixed $filepath
     * @return void
     */
    static function beforeDownloadDocument($documentId, $filepath, $filename) {
        $file = $filepath;

        if(Session::haveRight("plugin_encryptfile_encrypt", READ)) {
            $PluginEncryptfileConfig = new PluginEncryptfileConfig();

            // Check if it is an encrypted file
            $secretKeyId = $PluginEncryptfileConfig->isEncrypted($documentId);
            
            if($secretKeyId) {
                $secretKey = $PluginEncryptfileConfig->getSecretKey($_SESSION["glpiactiveprofile"]["id"], $secretKeyId);

                // If secretKey empty, maybe it have only read right
                if(is_null($secretKey)) $secretKey = $PluginEncryptfileConfig->canRead($_SESSION["glpiactiveprofile"]["id"], $secretKeyId);
    
                if(!is_null($secretKey)) {
                    $file = PluginEncryptfileEncrypt::decryptFile(PluginEncryptfileEncrypt::decryptkey($secretKey), $filepath, $filename);
                }
            }
        }

        return $file;
    }
} 