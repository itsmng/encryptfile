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
                    if ((new Plugin())->isActivated('formcreator')) {
                        $tab[3] = __("Formcreator configuration", "encryptfile");
                    }
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
                    case 3 :
                        $item->showFormcreatorForm();
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

    public static function giveItem($itemtype, $option_id, $data, $num) {
        return '';
    }
    
    /**
     * rawSearchOptions
     *
     * @return void
     */
    function rawSearchOptions() {
        $tab = [];
  
        $tab[] = [
           'id'                 => 'common',
           'name'               => __('Characteristics')
        ];
  
        $tab[] = [
           'id'                 => '1',
           'table'              => $this->getTable(),
           'field'              => 'name',
           'name'               => __('Title'),
           'searchtype'         => 'contains',
           'datatype'           => 'itemlink',
           'massiveaction'      => false,
           'autocomplete'       => true,
        ];
  
        $tab[] = [
           'id'                 => '16',
           'table'              => $this->getTable(),
           'field'              => 'comment',
           'name'               => __('Comments'),
           'datatype'           => 'text'
        ];
  
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
        Dropdown::showFromArray("status", array(0 => __("No"), 1 => __("Yes")), array('value' => $this->fields["status"]));
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
        $rand = mt_rand();

        $this->showFormHeader(["formtitle" => __("Profile configuration", "encryptfile")]);

        $dd_params = [
            'name'      => 'profiles_id_reading',
            'values'    => $this->getReadingProfiles($_GET["id"]),
            'display'   => true,
            'rand'      => $rand,
            'multiple'  => true,
            'size'      => 3
        ];
        
        echo "<tr class='tab_bg_1'><td width='50%'>".__('Select reading profiles', 'encryptfile')."</td><td>";
        Dropdown::showFromArray($dd_params['name'], $this->getProfiles(), $dd_params);
        echo "</td></tr>";

        $this->showFormButtons(['candel' => false]);
    
        return true;
    }
    
    /**
     * showItemForm
     *
     * @return void
     */
    function showItemForm() {
        $rand = mt_rand();

        $this->showFormHeader(["formtitle" => __("Item configuration", "encryptfile")]);
        
        $glpiObjects = [
            Ticket::class => Ticket::getTypeName(),
            Document::class => Document::getTypeName()
        ];

        foreach(get_declared_classes() as $class){
            if(Document::canApplyOn($class)) $glpiObjects[$class] = $class::getTypeName();
        }

        uasort($glpiObjects, function ($a, $b) {
                return strcmp($a,$b);
            }
        );

        $dd_params = [
            'name'      => 'itemtype',
            'values'    => $this->getItemtype($_GET["id"]),
            'display'   => true,
            'rand'      => $rand,
            'multiple'  => true,
            'size'      => 3
        ];
        
        echo "<tr class='tab_bg_2'><td width='50%'>".__('Select GLPi object', 'encryptfile')."</td><td>";
        Dropdown::showFromArray($dd_params['name'], $glpiObjects, $dd_params);
        echo "</td></tr>";

        $this->showFormButtons(['candel' => false]);
    
        return true;
    }
    
    /**
     * showFormcreatorForm
     *
     * @return void
     */
    function showFormcreatorForm() {
        $rand = mt_rand();

        $this->showFormHeader(["formtitle" => __("Formcreator configuration", "encryptfile"), "colspan" => 3]);

        $PluginFormcreatorForm = new PluginFormcreatorForm();
        $formsForm = $PluginFormcreatorForm->find();

        $forms = [
            "0" => "-----"
        ];

        foreach($formsForm as $key => $value) $forms[$key] = $value["name"];

        echo "<tr class='tab_bg_1'>";
        echo "<td>".__('Form', 'formcreator')."</td><td>";
        Dropdown::showFromArray("forms_id", $forms, array("on_change" => "displayFormSection();"));
        echo "</td>";

        echo "<td>".__('Section', 'formcreator')."</td><td id='sections_to_replace'>";
        Dropdown::showFromArray("sections_id", array("0" => "-----"), array());
        echo "</td>";

        echo "<td>".__('Question', 'formcreator')."</td><td id='questions_to_replace'>";
        Dropdown::showFromArray("questions_id", array("0" => "-----"), array());
        echo "</td>";
        echo "</tr>";

        $this->showFormButtons(['candel' => false, "colspan" => 3]);
    
        return true;
    }
    
    /**
     * getProfiles
     *
     * @return void
     */
    function getProfiles() {
        $Profile = new Profile();
        $allProfiles = $Profile->find();

        $profiles = [];

        foreach($allProfiles as $key => $values) {
            $profiles[$key] = $values["name"];
        }

        return $profiles;
    }
    
    /**
     * getItemtype
     *
     * @param  mixed $id
     * @param  mixed $itemtype
     * @return void
     */
    function getItemtype($id, $itemtype = null) {
        global $DB;

        $query = "SELECT itemtype FROM `glpi_plugin_encryptfile_items` WHERE keys_id = $id";
        if(!is_null($itemtype)) $query .= " AND itemtype = '$itemtype'";

        $result = $DB->query($query);

        $itemTypes = [];

        if($result) foreach($result as $key => $values) {
            $itemTypes[$values["itemtype"]] = $values["itemtype"];
        }

        return $itemTypes;
    }
        
    /**
     * getReadingProfiles
     *
     * @param  mixed $id
     * @param  mixed $profiles_id
     * @return void
     */
    function getReadingProfiles($id, $profiles_id = null) {
        global $DB;

        $query = "SELECT profiles_id FROM `glpi_plugin_encryptfile_profiles` WHERE keys_id = $id";
        if(!is_null($profiles_id)) $query .= " AND profiles_id = $profiles_id";

        $result = $DB->query($query);

        $readingProfiles = [];

        if($result) foreach($result as $key => $values) {
            $readingProfiles[$values["profiles_id"]] = $values["profiles_id"];
        }

        return $readingProfiles;
    }
    
    /**
     * updateReadingProfiles
     *
     * @param  mixed $id
     * @param  mixed $post
     * @return void
     */
    public function updateReadingProfiles($id, $post) {
        global $DB;

        // Clean reading profiles before update
        $this->removeReadingProfiles($id);

        foreach($post as $profile_id) {
            $query = "INSERT INTO `glpi_plugin_encryptfile_profiles`(keys_id, profiles_id) VALUES($id, $profile_id)";
            $DB->query($query);
        }
    }
    
    /**
     * updateItemtype
     *
     * @param  mixed $id
     * @param  mixed $post
     * @return void
     */
    public function updateItemtype($id, $post) {
        global $DB;

        // Clean itemtype before update
        $this->removeItemtype($id);

        foreach($post as $itemtype) {
            $query = "INSERT INTO `glpi_plugin_encryptfile_items`(keys_id, itemtype) VALUES($id, '$itemtype')";
            $DB->query($query);
        }
    }
    
    /**
     * updateFormcreator
     *
     * @param  mixed $id
     * @param  mixed $form_id
     * @param  mixed $sections_id
     * @param  mixed $questions_id
     * @return void
     */
    public function updateFormcreator($id, $form_id, $sections_id, $questions_id) {
        global $DB;

        $query = "INSERT INTO `glpi_plugin_encryptfile_formcreator`(keys_id,forms_id,sections_id,questions_id) VALUES($id,$form_id,$sections_id,$questions_id)";
        $DB->query($query);
    }
    
    /**
     * removeItemtype
     *
     * @param  mixed $id
     * @return void
     */
    function removeItemtype($id) {
        global $DB;

        $DB->query("DELETE FROM `glpi_plugin_encryptfile_items` WHERE keys_id = $id");
    }
    
    /**
     * removeReadingProfiles
     *
     * @param  mixed $id
     * @return void
     */
    function removeReadingProfiles($id) {
        global $DB;

        $DB->query("DELETE FROM `glpi_plugin_encryptfile_profiles` WHERE keys_id = $id");
    }
    
    /**
     * getSecretKey
     *
     * @param  mixed $activeProfile
     * @return void
     */
    public function getSecretKey($activeProfile, $secretKeyId = null) {
        $secretKey = null;

        if(!is_null($secretKeyId)) {
            $search = ["profiles_id" => $activeProfile, "id" => $secretKeyId];
        } else {
            $search = ["profiles_id" => $activeProfile];
        }

        $result = $this->find($search);
        if($result) foreach($result as $values) {
            // Only if key is actived
            if($values["status"]) {
                $secretKey = $values["key"];
            }
        }

        return $secretKey;
    }
    
    /**
     * getSecretKeyId
     *
     * @param  mixed $activeProfile
     * @return void
     */
    public function getSecretKeyId($activeProfile) {
        $secretKeyId = null;

        $result = $this->find(["profiles_id" => $activeProfile]);
        if($result) foreach($result as $values) {
            // Only if key is actived
            if($values["status"]) {
                $secretKeyId = $values["id"];
            }
        }

        return $secretKeyId;
    }
    
    /**
     * saveDocumentInfo
     *
     * @param  mixed $secretKeyId
     * @param  mixed $documentId
     * @return void
     */
    public function saveDocumentInfo($secretKeyId, $documentId) {
        global $DB;

        $query = "INSERT INTO `glpi_plugin_encryptfile_documents`(keys_id, documents_id) VALUES($secretKeyId, $documentId)";
        $DB->query($query);

        return true;
    }
    
    /**
     * canRead
     *
     * @param  mixed $activeProfile
     * @param  mixed $secretKeyId
     * @return void
     */
    public function canRead($activeProfile, $secretKeyId) {
        global $DB;
        
        $secretKey = null;

        $query = "SELECT c.key, c.status FROM `glpi_plugin_encryptfile_configs` c LEFT JOIN `glpi_plugin_encryptfile_profiles` p on c.id = p.keys_id WHERE p.profiles_id = $activeProfile AND p.keys_id = $secretKeyId";
        $result = $DB->query($query);

        if($result) foreach($result as $values) {
            if($values["status"]) {
                $secretKey = $values["key"];
            }
        }

        return $secretKey;
    }
    
    /**
     * isEncrypted
     *
     * @param  mixed $documentId
     * @return void
     */
    public function isEncrypted($documentId) {
        global $DB;

        $secretKeyId = null;

        $query = "SELECT keys_id FROM `glpi_plugin_encryptfile_documents` WHERE documents_id = $documentId";
        $result = $DB->query($query);

        if($result) foreach($result as $values) {
            $secretKeyId = $values["keys_id"];
        }

        return $secretKeyId;
    }
    
    /**
     * getAuthorizedItem
     *
     * @param  mixed $secretKeyId
     * @return void
     */
    public function getAuthorizedItem($secretKeyId) {
        global $DB;

        $itemtypes = [];

        $query = "SELECT itemtype FROM `glpi_plugin_encryptfile_items` WHERE keys_id = $secretKeyId";
        $result = $DB->query($query);

        if($result) foreach($result as $values) {
            $itemtypes[] = $values["itemtype"]::getFormURL();
        }

        return $itemtypes;
    }
    
    /**
     * afterPurgeDocument
     *
     * @param  mixed $post
     * @return void
     */
    static function afterPurgeDocument(Document $post) {
        global $DB;

        $query = "DELETE FROM `glpi_plugin_encryptfile_documents` WHERE documents_id = ".$post->fields["id"];
        $DB->query($query);
    }
}