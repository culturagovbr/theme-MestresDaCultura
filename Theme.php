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
            // Tira as telas que o subsite não usa (MDC\Plugin::UNUSED_PANEL_SCREENS).
            $unused = array_map(fn($action) => "panel/{$action}", class_exists(\MDC\Plugin::class) ? \MDC\Plugin::UNUSED_PANEL_SCREENS : []);
            $semUso = function (array $grupo) use ($unused) {
                $grupo['items'] = array_values(array_filter($grupo['items'] ?? [], fn($item) => !in_array($item['route'] ?? '', $unused, true)));
                return $grupo;
            };

            // saasSuperAdmin mantém o menu do core, sem as condições que app.enabled.* impõe, e só ganha o
            // grupo do programa.
            if ($app->user->is('saasSuperAdmin')) {
                foreach ($nav_items as &$grupo) {
                    unset($grupo['condition']);

                    if (isset($grupo['items'])) {
                        $grupo = $semUso($grupo);

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

            // Os grupos do core como eles são, menos o que o subsite não usa: "Menu do Painel de Controle" (o caminho
            // de volta a "Meus cadastros"), "Outras opções" (com a moderação, para quem modera) e o do usuário logado
            // (Meu Perfil, Sair). Entre eles, o grupo do programa.
            $grupos = array_filter(['panel-menu' => $nav_items['panel-menu'] ?? null]) + ['mdc-criar' => $criarCadastro];

            if (isset($nav_items['more'])) {
                $grupos['more'] = $semUso($nav_items['more']);
                $grupos['more']['items'] = array_merge($grupos['more']['items'], $avaliacoes);
            }

            // Administração segue como o core e os plugins montaram. Sem "Minhas oportunidades": o gestor chega ao
            // programa pela Gestão do cadastro (MDC\Plugin::addManagementShortcuts).
            if (isset($nav_items['admin'])) {
                $grupos['admin'] = $nav_items['admin'];
            }

            if (isset($nav_items['user'])) {
                $grupos['user'] = $nav_items['user'];
            }

            $nav_items = $grupos;
        }, 1000);

        // No breadcrumb da oportunidade, tira "Minhas oportunidades" fora do saasSuperAdmin e chama "Minhas avaliações"
        // de "Moderação de cadastros".
        $app->hook('Theme::addOpportunityBreadcramb', function () use ($app) {
            $crumbs = $this->breadcrumb ?? [];

            if (!$app->user->is('saasSuperAdmin')) {
                $mine = $app->createUrl('panel', 'opportunities');
                $crumbs = array_filter($crumbs, fn($crumb) => ($crumb['url'] ?? null) !== $mine);
            }

            $this->breadcrumb = array_values(array_map(function ($crumb) {
                if (($crumb['label'] ?? null) === i::__('Minhas avaliações')) {
                    $crumb['label'] = i::__('Moderação de cadastros');
                }

                return $crumb;
            }, $crumbs));
        });
    }
}
