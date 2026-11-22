<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

$this->import('
    home-banner
    home-cadastro
    home-festejos
    home-mestres
    mdc-network-map
');
?>
<home-banner></home-banner>

<div class="container">
    <home-mestres></home-mestres>
    <home-cadastro></home-cadastro>
    <home-festejos></home-festejos>
    <mdc-network-map></mdc-network-map>
</div>
