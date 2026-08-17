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
            $iconset['registration'] = 'mdi:file-account';
        });

        $app->hook('panel.nav', function (&$nav_items) {
            $nav_items = ['mdc-create' => [
                'label' => \MapasCulturais\i::__('Criar novo cadastro'),
                'column' => 'left',
                'items' => [
                    ['route' => 'panel/events', 'icon' => 'celebration', 'label' => \MapasCulturais\i::__('Criar Cadastro de Festejo Popular')],
                ],
            ]] + $nav_items;
        });
    }
}
