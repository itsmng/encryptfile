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
include("../../../inc/includes.php");

$checkbox = '<input type="hidden" id="encryptfile">';

if(isset($_GET["item_url"])) {
    if(Session::haveRight("plugin_encryptfile_encrypt", UPDATE)) {
        $PluginEncryptfileConfig = new PluginEncryptfileConfig();
        
        $secretKeyId = $PluginEncryptfileConfig->getSecretKeyId($_SESSION["glpiactiveprofile"]["id"]);
    
        if(!is_null($secretKeyId)) {
            $glpiItems = $PluginEncryptfileConfig->getAuthorizedItem($secretKeyId);

            foreach($glpiItems as $item) {
                if(strpos($_GET["item_url"], $item) !== false) {
                    $checkbox = '<div class="row" style="margin-bottom:10px;"><span class="form-group-checkbox"><input type="checkbox" class="new_checkbox" id="encryptfile" name="encryptfile" value="1" data-glpicore-ma-tags="common"><label class="label-checkbox" title="" for="encryptfile"> <span class="check"></span> <span class="box"></span>&nbsp;</label></span><label for="encryptfile">&nbsp;Chiffrer le document</label></div>';
                }
            }
        }
    }
}

echo $checkbox;