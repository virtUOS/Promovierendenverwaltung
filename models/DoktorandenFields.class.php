<?php



/**
 * @author  <asudau@uos.de>
 *
 * @property int        $chdate
 */

class DoktorandenFields extends \SimpleORMap
{
   
    protected static function configure($config = array())
    {
        $config['db_table'] = 'doktorandenverwaltung_fields';
        
        $config['additional_fields']['values']['get'] = function ($item){
            return DoktorandenFieldValue::findBySQL("field_id LIKE ?", array($item->getIdOfValues()) );
        };
        
        $config['additional_fields']['search_object']['get'] = function ($item) {
            if (DoktorandenFieldValue::findOneBySQL("field_id LIKE ?", array($item->getIdOfValues()))){
                if ($item->value_key != NULL){
                    return new SQLSearch("SELECT " . $item->value_key . ", CONCAT(defaulttext, ' (' , uniquename, ')') as title " .
                    "FROM doktorandenverwaltung_field_values WHERE `field_id` LIKE '" . $item->getIdOfValues() . "' " .
                    "AND (`defaulttext` LIKE :input OR `uniquename` LIKE :input)", _($item->title), "field_id");
                }
            } else return NULL;

        };

        parent::configure($config);
    }
    
    public static function getHeaderFields() {
         
        return self::findBySQL('overview_position > 0 ORDER BY overview_position ASC');
    }
    
    public static function getExportFieldsArray(){
        $fields = self::findBySQL("export_name != '0' ORDER BY export_name ASC");
        return $fields;
    }
    
    public static function getFullExportFieldsArray(){
        $fields = self::findBySQL("true ORDER BY export_name ASC");
        return $fields;
    }
    
    public function getValueTextByKey($key = null) {
        if($this->value_key != NULL && $key){
            $value = DoktorandenFieldValue::findOneBySQL("field_id = ? AND " . $this->value_key . " = '" . $key . "'", array($this->getIdOfValues()));
            return $value['defaulttext'];
        } else return false;
    }
    
     public function getValueLIDByUniquename($key = null) {
        if($this->value_key != NULL && $key){
            if (strlen($key) > 3){
                $key = substr($key, -3);
            }
            $value = DoktorandenFieldValue::findOneBySQL("field_id = ? AND uniquename = '" . $key . "'", array($this->getIdOfValues()));
            return $value['lid'];
        } else return false;
    }
    
    //Astat-Werte für Berichtsexport zusammenstellen (inklusive weiterer Randbedingungen)
    public function getValueAstatByKey($key = null){
        if($this->value_key != NULL && $key){
            $value = DoktorandenFieldValue::findOneBySQL("field_id = ? AND " . $this->value_key . " = '" . $key . "'", array($this->getIdOfValues()));
            
            //Sonderfälle
            if($this->id == 'promotionsfach'){
                return substr($value['astat_bund'], -3);
            } 
            
            return $value['astat_bund'];
    
        //Sonderfälle bei fehlenden Einträgen
        } else if($key == NULL) {
            switch($this->id){
                case 'staatsangehoerigkeit':
                    return '999';
                case 'weitere_staatsangehoerigkeit':
                    return '999';
                case 'promotionsende_monat':
                    return '12';
                case 'promotionsende_jahr':
                    return date("Y");
            }
        }

        return false;
    }
    
    public static function getRequiredFields() {
        return self::findBySQL("fill = 'manual_req'" );
    }
    
    public function getIdOfValues(){
      return $this->value_id ? $this->value_id : $this->id;
    }
       
}
