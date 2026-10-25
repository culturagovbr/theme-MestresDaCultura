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
                ['route' => 'mdc/index', 'icon' => 'master', 'label' => i::__('Criar Cadastro de Mestre/Mestra')],
                ['route' => 'event/create', 'icon' => 'celebration', 'label' => i::__('Criar Cadastro de Festejo Popular')],
            ],
        ];

        // Prioridade alta para rodar depois dos módulos que alimentam a lista.
        $app->hook('panel.nav', function (&$nav_items) use ($app, $criarCadastro) {
            // saasSuperAdmin não é restringido: mantém o menu do core, sem as condições que
            // app.enabled.* impõe, e só ganha o grupo do programa. Os filtros do plugin (inscrições,
            // painel, selos, notificações) isentam também o admin (MDC\Plugin::appliesToCurrentUser).
            // Sem as telas que o subsite não usa, que não abrem nem para ele (MDC\Plugin::UNUSED_PANEL_SCREENS).
            if ($app->user->is('saasSuperAdmin')) {
                $unused = array_map(fn($action) => "panel/{$action}", class_exists(\MDC\Plugin::class) ? \MDC\Plugin::UNUSED_PANEL_SCREENS : []);

                foreach ($nav_items as &$grupo) {
                    unset($grupo['condition']);

                    if (isset($grupo['items'])) {
                        $grupo['items'] = array_values(array_filter($grupo['items'], fn($item) => !in_array($item['route'] ?? '', $unused, true)));

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

            // Administração segue como o core e os plugins montaram. Sem "Minhas oportunidades": o gestor chega ao
            // programa pela Gestão do cadastro (MDC\Plugin::addManagementShortcuts).
            if (isset($nav_items['admin'])) {
                $grupos['admin'] = $nav_items['admin'];
            }

            $nav_items = $grupos;
        }, 1000);

        // O core põe "Minhas oportunidades" no breadcrumb das telas da oportunidade de quem não é avaliador da fase;
        // fora do saasSuperAdmin, ninguém tem essa página no menu. Como o Cultura Viva filtra o dele.
        $app->hook('Theme::addOpportunityBreadcramb', function () use ($app) {
            if (!$app->user->is('saasSuperAdmin')) {
                $mine = $app->createUrl('panel', 'opportunities');
                $this->breadcrumb = array_values(array_filter($this->breadcrumb ?? [], fn($crumb) => ($crumb['url'] ?? null) !== $mine));
            }
        });
    }
}
