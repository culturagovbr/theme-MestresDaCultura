<?php

namespace MestresDaCultura;

class Theme extends \MapasCulturais\Themes\BaseV2\Theme
{
    static function getThemeFolder()
    {
        return __DIR__;
    }

    function _init()
    {
        parent::_init();

        $app = \MapasCulturais\App::i();

        $this->enqueueStyle('app-v2', 'main', 'css/theme-MestresDaCultura.css');

        $app->hook('component(mc-icon).iconset', function (&$iconset) {
            $iconset['master'] = 'mdi:account-star';
            $iconset['celebration'] = 'mdi:party-popper';
        });
    }
}
