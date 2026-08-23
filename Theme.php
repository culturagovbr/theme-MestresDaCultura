<?php

namespace MestresDaCultura;

use MapasCulturais\i;

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

        $criarCadastro = [
            'label' => i::__('Criar novo cadastro'),
            'column' => 'left',
            'items' => [
                // Sem rota até a oportunidade fixa existir; renderiza sem link.
                ['icon' => 'master', 'label' => i::__('Criar Cadastro de Mestre/Mestra')],
                ['route' => 'event/create', 'icon' => 'celebration', 'label' => i::__('Criar Cadastro de Festejo Popular')],
            ],
        ];

        // Prioridade alta para rodar depois dos módulos que alimentam a lista.
        $app->hook('panel.nav', function (&$nav_items) use ($app, $criarCadastro) {
            // saasSuperAdmin não é restringido: mantém o menu do core, sem as condições que
            // app.enabled.* impõe, e só ganha o grupo do programa.
            if ($app->user->is('saasSuperAdmin')) {
                foreach ($nav_items as &$grupo) {
                    unset($grupo['condition']);

                    if (isset($grupo['items'])) {
                        foreach ($grupo['items'] as &$item) {
                            unset($item['condition']);
                        }
                        unset($item);
                    }
                }
                unset($grupo);

                $nav_items = ['mdc-criar' => $criarCadastro] + $nav_items;

                return;
            }

            // Preserva o item original, que carrega a condição de avaliador.
            $avaliacoes = array_values(array_filter(
                $nav_items['opportunities']['items'] ?? [],
                fn($item) => ($item['route'] ?? '') === 'panel/evaluations'
            ));

            $oportunidades = array_map(function ($item) use ($app) {
                $item['condition'] = fn() => $app->user->is('admin');
                return $item;
            }, array_values(array_filter(
                $nav_items['opportunities']['items'] ?? [],
                fn($item) => ($item['route'] ?? '') === 'panel/opportunities'
            )));

            $grupos = [
                'mdc-criar' => $criarCadastro,
                // Coluna "user": o componente a renderiza depois de um traço e sem título.
                'user' => [
                    'label' => '',
                    'column' => 'user',
                    'items' => array_merge([
                        ['route' => 'panel/my-account', 'icon' => 'account', 'label' => i::__('Conta e Privacidade')],
                    ], $avaliacoes, [
                        ['route' => 'auth/logout', 'icon' => 'logout', 'label' => i::__('Sair')],
                    ]),
                ],
            ];

            // Administração segue como o core e os plugins montaram, com os itens de gestão.
            if (isset($nav_items['admin'])) {
                $nav_items['admin']['items'] = array_merge($nav_items['admin']['items'] ?? [], $oportunidades);
                $grupos['admin'] = $nav_items['admin'];
            }

            $nav_items = $grupos;
        }, 1000);
    }
}
