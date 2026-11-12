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

        // Os grupos do programa no menu do painel: o cadastro de mestra e os festejos, com os locais da agenda.
        $mestres = [
            'label' => i::__('Mestras e Mestres'),
            'column' => 'left',
            'items' => [
                ['route' => 'mdc/index', 'icon' => 'master', 'label' => i::__('Cadastro de Mestres e Mestras')],
            ],
        ];
        $festejos = [
            'label' => i::__('Festejos Populares'),
            'column' => 'left',
            'items' => [
                ['route' => 'mdc/festivity', 'icon' => 'celebration', 'label' => i::__('Cadastrar festejo')],
                ['route' => 'mdc/myFestivities', 'icon' => 'event', 'label' => i::__('Meus festejos')],
                ['route' => 'mdc/places', 'icon' => 'map-pin', 'label' => i::__('Meus locais')],
            ],
        ];

        // Monta o menu do painel depois dos módulos que alimentam a lista.
        $app->hook('panel.nav', function (&$nav_items) use ($app, $mestres, $festejos) {
            // Tira as telas que o subsite não usa (MDC\Plugin::UNUSED_PANEL_SCREENS).
            $unused = array_map(fn($action) => "panel/{$action}", class_exists(\MDC\Plugin::class) ? \MDC\Plugin::UNUSED_PANEL_SCREENS : []);
            $semUso = function (array $grupo) use ($unused) {
                $grupo['items'] = array_values(array_filter($grupo['items'] ?? [], fn($item) => !in_array($item['route'] ?? '', $unused, true)));
                return $grupo;
            };

            // "Painel de Controle" sozinho no alto, sem o título do grupo.
            $painel = isset($nav_items['panel-menu']) ? ['panel-menu' => ['label' => ''] + $nav_items['panel-menu']] : [];

            // O saasSuperAdmin vê o menu do core sem condições, sem "Meus Eventos" e com os grupos do programa no alto.
            if ($app->user->is('saasSuperAdmin')) {
                foreach ($nav_items as &$grupo) {
                    unset($grupo['condition']);

                    if (isset($grupo['items'])) {
                        $grupo = $semUso($grupo);
                        $grupo['items'] = array_values(array_filter($grupo['items'], fn($item) => ($item['route'] ?? '') !== 'panel/events'));

                        foreach ($grupo['items'] as &$item) {
                            unset($item['condition']);
                        }
                        unset($item);
                    }
                }
                unset($grupo);

                $nav_items = $painel + ['mdc-mestres' => $mestres, 'mdc-festejos' => $festejos] + $nav_items;

                return;
            }

            // Leva o item de avaliações, com a condição de avaliador, para o grupo do cadastro de mestra.
            $mestres['items'] = array_merge($mestres['items'], array_values(array_filter(
                $nav_items['opportunities']['items'] ?? [],
                fn($item) => ($item['route'] ?? '') === 'panel/evaluations'
            )));

            $grupos = $painel + ['mdc-mestres' => $mestres, 'mdc-festejos' => $festejos];

            // Na coluna da direita, "Minha conta" (Conta e Privacidade) e o do usuário logado (Meu Perfil, Sair).
            if (isset($nav_items['more'])) {
                $grupos['more'] = ['label' => i::__('Minha conta')] + $semUso($nav_items['more']);
            }

            // Administração como o core e os plugins montaram.
            if (isset($nav_items['admin'])) {
                $grupos['admin'] = $nav_items['admin'];
            }

            if (isset($nav_items['user'])) {
                $grupos['user'] = $nav_items['user'];
            }

            $nav_items = $grupos;
        }, 1000);

        // No breadcrumb da oportunidade, tira "Minhas oportunidades" fora do saasSuperAdmin e chama "Minhas avaliações"
        // de "Moderação de cadastros", ou "Moderação de festejos" na oportunidade dos festejos.
        $app->hook('Theme::addOpportunityBreadcramb', function ($unused, $label, $entity) use ($app) {
            $crumbs = $this->breadcrumb ?? [];
            $opportunity = $entity instanceof \MapasCulturais\Entities\EvaluationMethodConfiguration ? $entity->opportunity : $entity;
            $festivity = isset($app->plugins['MDC']) && $app->plugins['MDC']->programOpportunities()->isFestivityOpportunity($opportunity);
            $moderation = $festivity ? i::__('Moderação de festejos') : i::__('Moderação de cadastros');

            if (!$app->user->is('saasSuperAdmin')) {
                $mine = $app->createUrl('panel', 'opportunities');
                $crumbs = array_filter($crumbs, fn($crumb) => ($crumb['url'] ?? null) !== $mine);
            }

            $this->breadcrumb = array_values(array_map(function ($crumb) use ($moderation) {
                if (($crumb['label'] ?? null) === i::__('Minhas avaliações')) {
                    $crumb['label'] = $moderation;
                }

                return $crumb;
            }, $crumbs));
        });
    }
}
