<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

$this->import('
    home-banner
    home-cadastro
    home-mestres
');
?>
<home-banner></home-banner>

<div class="container">
    <home-mestres></home-mestres>
    <home-cadastro></home-cadastro>
</div>
