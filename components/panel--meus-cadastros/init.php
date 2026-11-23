<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

$opportunity = $app->plugins['MDC']->fixedOpportunity()->get();

$this->jsObject['config']['panelMeusCadastros'] = [
    'opportunityId' => $opportunity?->id,
    'subsiteId' => $app->getCurrentSubsiteId(),
    // os nomes dos status configurados na oportunidade ("Em análise", "Aprovado")
    'statusLabels' => $opportunity?->statusLabels ?: new stdClass,
];

// A primeira página das inscrições de festejo de quem tem a conta, a mesma lista de "Meus festejos".
$festivities = \MDC\Services\FestivityList::page($app->user);
$this->jsObject['config']['panelMeusCadastros']['festivities'] = $festivities['festivities'];
$this->jsObject['config']['panelMeusCadastros']['moreFestivities'] = $festivities['more'];
