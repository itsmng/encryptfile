<?php
/*** 
-------------------------------------------------------------------------
 encryptfile plugin for GLPI
-------------------------------------------------------------------------
 ***/

class PluginEncryptfileEncrypt extends CommonDBTM {
    static $rightname = 'plugin_encryptfile_encrypt';

    public static function getTypeName($nb = 1) {
        return _n('Encrypted file', 'Encrypted files', $nb, 'encryptfile');
    }
} 