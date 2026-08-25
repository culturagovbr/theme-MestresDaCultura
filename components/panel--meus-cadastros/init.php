<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

$opportunity = $app->plugins['MDC']->fixedOpportunity()->get();

$this->jsObject['config']['panelMeusCadastros'] = [
    'opportunityId' => $opportunity?->id,
    'subsiteId' => $app->getCurrentSubsiteId(),
];
