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

class PluginEncryptfileConfig extends CommonDBTM {
    static $rightname = 'plugin_encryptfile_configs';
    
    /**
     * getTypeName
     *
     * @param  mixed $nb
     * @return void
     */
    public static function getTypeName($nb = 1) {
        return _n('Generate key', 'Generate keys', $nb, 'encryptfile');
    }
    
    /**
     * getMenuContent
     *
     * @return void
     */
    static function getMenuContent() {
        $menu = array();

        $menu['title']              = self::getTypeName(2);
        $menu['page']               = "/plugins/encryptfile/front/config.php";
        $menu['icon']               = "fas fa-key";
        $menu['links']['search']    = "/plugins/encryptfile/front/config.php";
        $menu['links']['add']       = "/plugins/encryptfile/front/config.form.php";
        
        return $menu;
    }
    
    /**
     * getTabNameForItem
     *
     * @param  mixed $item
     * @param  mixed $withtemplate
     * @return void
     */
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        if (!$withtemplate) {
            switch ($item->getType()) {
                case __CLASS__ :
                    $tab[1] = __("Profile configuration", "encryptfile");
                    $tab[2] = __("Item configuration", "encryptfile");
                    return $tab;
            }
        }
        return "";
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
        switch ($item->getType()) {
            case __CLASS__ :
                switch ($tabnum) {
                    case 1 :
                        $item->showProfileForm();
                        break;
                    case 2 :
                        $item->showItemForm();
                        break;
                }

                break;
        }
        return true;
    }
    
    /**
     * defineTabs
     *
     * @param  mixed $options
     * @return void
     */
    function defineTabs($options = []) {
        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addStandardTab(__CLASS__, $ong, $options);
        return $ong;
    }
    
    /**
     * getSearchOptions
     *
     * @return void
     */
    public function getSearchOptions() {
        $tab = array();
        $tab['common'] = __s('Characteristics');

        $tab[1]['table'] = $this->getTable();
        $tab[1]['field'] = 'name';
        $tab[1]['name'] = __s('Name');
        $tab[1]['datatype'] = 'itemlink';
        $tab[1]['massiveaction'] = false;

        $tab[2]['table'] = $this->getTable();
        $tab[2]['field'] = 'id';
        $tab[2]['name'] = __s('ID');
        $tab[2]['massiveaction'] = false;
        $tab[2]['datatype'] = 'number';

        $tab[3]['table'] = $this->getTable();
        $tab[3]['field'] = 'comment';
        $tab[3]['name'] = __s('Comments');
        $tab[3]['datatype'] = 'text';
        $tab[3]['massiveaction'] = false;

        return $tab;
    }
    
    /**
     * showForm
     *
     * @param  mixed $ID
     * @param  mixed $options
     * @return void
     */
    public function showForm($ID, $options = array()) {
        $rowspan = 2;

        $this->initForm($ID, $options);
        $this->showFormHeader($options);
        
        echo "<tr class='tab_bg_1'><td>".__('Name')."</td><td>";
        Html::autocompletionTextField($this, "name", array('value' => $this->fields["name"]));
        echo "</td>";

        echo "<td rowspan='".$rowspan."'>".__('Comments')."</td>";
        echo "<td rowspan='".$rowspan."'>";
        echo "<textarea cols='45' rows='4' name='comment' >".$this->fields["comment"]."</textarea>";
        echo "</td></tr>";

        echo "<tr class='tab_bg_1'><td>".__('Default profile')."</td><td>";
        Profile::Dropdown(array('name' => 'profiles_id', 'value' => $this->fields["profiles_id"]));
        echo "</td></tr>";

        echo "<tr class='tab_bg_1'><td>".__('Active')."</td><td>";
        Dropdown::ShowFromArray("status", array(0 => __("No"), 1 => __("Yes")), array('value' => $this->fields["status"]));
        echo "</td></tr>";

        $this->showFormButtons($options);
    
        return true;
    }
    
    /**
     * showProfileForm
     *
     * @return void
     */
    function showProfileForm() {
        $this->showFormHeader();
        
        echo "temp";

        $this->showFormButtons();
    
        return true;
    }
    
    /**
     * showItemForm
     *
     * @return void
     */
    function showItemForm() {
        $this->showFormHeader();
        
        echo "temp";

        $this->showFormButtons();
    
        return true;
    }
}