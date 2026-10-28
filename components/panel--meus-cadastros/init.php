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
    // o formulário do festejo (`/cadastrar-festejo/{id}/`), para os festejos de quem cadastra
    'festivityFormUrl' => $app->createUrl('mdc', 'festivity'),
];
