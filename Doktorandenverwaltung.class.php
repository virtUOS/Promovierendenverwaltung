<?php

/**
 * Doktorandenverwaltung.class.php
 *
 * ...
 *
 * @author  Annelene Sudau <asudau@uos.de>
 * @version 0.1a
 */


class Doktorandenverwaltung extends StudipPlugin implements SystemPlugin 
{

    const DOKTORANDEN_ADMIN_ROLE = 'Doktorandenverwaltung';
    
    public function __construct()
    {
        parent::__construct();
        global $perm;

        if(RolePersistence::isAssignedRole($GLOBALS['user']->user_id,
                                                            self::DOKTORANDEN_ADMIN_ROLE)){
            $navigation = new Navigation($this->getPluginName());
            $navigation->setImage(Icon::create('edit', 'navigation'));
            $navigation->setURL(PluginEngine::getURL($this, array(), 'index'));
            
            $item = new Navigation(_('Übersicht'), PluginEngine::getURL($this, array(), 'index'));
            $navigation->addSubNavigation('index', $item);
            $item = new Navigation(_('Admin-Übersicht'), PluginEngine::getURL($this, array(), 'index/admin'));
            $navigation->addSubNavigation('index_admin', $item);
            
            Navigation::addItem('/doktorandenverwaltung', $navigation);  
        }    
    }

    public function initialize ()
    {
        
    }

    public function perform($unconsumed_path)
    {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
        
    }

    private function setupAutoload()
    {
        if (class_exists('StudipAutoloader')) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }
}
