<?php

return [
    'app.siteName' => 'Rede de Mestras e Mestres',
    'app.siteDescription' => 'Rede Nacional de Mestras e Mestres das Culturas Tradicionais e Populares.',

    // Sem padrão: o id muda por ambiente, e um padrão apontaria para a oportunidade errada.
    'mdc.opportunityId' => env('MDC_OPPORTUNITY_ID'),

    // entidades habilitadas
    // Oportunidades segue habilitada porque as inscrições dependem dela; o que se remove é a navegação.
    'app.enabled.agents'        => false,
    'app.enabled.spaces'        => false,
    'app.enabled.projects'      => false,
    'app.enabled.events'        => true,
    'app.enabled.opportunities' => true,
];
