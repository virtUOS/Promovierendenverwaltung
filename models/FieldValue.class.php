<?php

/**
 * @author  <asudau@uos.de>
 *
 * @property int        $chdate
 */

class FieldValue extends \SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'doktorandenverwaltung_field_values';
        
        parent::configure($config);
    }
}
