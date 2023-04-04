<?php
/*** 
-------------------------------------------------------------------------
 encryptfile plugin for GLPI
-------------------------------------------------------------------------
 ***/

class PluginEncryptfileProfile extends Profile {
    
    /**
     * getTabNameForItem
     *
     * @param  mixed $item
     * @param  mixed $withtemplate
     * @return void
     */
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        return self::createTabEntry(__('Encrypted file', 'encryptfile'));
    }
    
    /**
     * displayTabContentForItem
     *
     * @param  mixed $item
     * @param  mixed $tabnum
     * @param  mixed $withtemplate
     * @return void
     */
    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        $encryptfileprofile = new self();
        $encryptfileprofile->showForm($item->getID());
        return true;
    }
    
    /**
     * showForm
     *
     * @param  mixed $profiles_id
     * @param  mixed $openform
     * @param  mixed $closeform
     * @return void
     */
    function showForm($profiles_id = 0, $openform = true, $closeform = true) {
        global $CFG_GLPI;

        if (!self::canView()) {
            return false;
        }

        echo "<div class='spaced'>";
        $profile = new Profile();
        $profile->getFromDB($profiles_id);

        if (($canedit = Session::haveRightsOr(self::$rightname, [CREATE, UPDATE, PURGE])) && $openform) {
            echo "<form method='post' action='".$profile->getFormURL()."'>";
        }

        $rights = [[
            'itemtype'  => 'PluginEncryptfileEncrypt',
            'label'     => PluginEncryptfileEncrypt::getTypeName(Session::getPluralNumber()),
            'rights'    => [READ =>__('Decrypt', 'encryptfile'), UPDATE => __('Encrypt', 'encryptfile')],
            'field'     => 'plugin_encryptfile_encrypt'
        ]];

        $matrix_options['title'] = __('Encrypted file', 'encryptfile');
        $profile->displayRightsChoiceMatrix($rights, $matrix_options);

        if ($canedit && $closeform) {
            echo "<div class='center'>";
            echo Html::hidden('id', ['value' => $profiles_id]);
            echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
            echo "</div>\n";
            Html::closeForm();
        }

        echo "</div>";
    }
}