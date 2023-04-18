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
        return base64_encode(sodium_crypto_secretbox_keygen());
    }
    
    /**
     * decryptkey
     *
     * @param  mixed $key
     * @return void
     */
    public function decryptkey($key) {
        return base64_decode($key);
    }
} 