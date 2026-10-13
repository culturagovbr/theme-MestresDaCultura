<?php
/**
 * Cópia de modules/Opportunities/views/registration/pdf.php, com os mesmos ganchos, os campos de
 * MestresDaCultura\RegistrationPdf, "Informações Básicas" só quando tem conteúdo e cartões inteiros por página.
 *
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 * @var MapasCulturais\Entities\Registration $registration
 */

use MapasCulturais\i;
use MestresDaCultura\RegistrationPdf;

// Obter logo da plataforma
$logoUrl = '';
$logoImage = $app->config['logo.image'] ?? '';
if ($logoImage) {
    $logoPath = $app->view->resolveFilename('assets', $logoImage);
    if (file_exists($logoPath)) {
        $logoUrl = $logoPath;
    }
}

$siteName = $app->config['logo.title'] . ' ' . $app->config['logo.subtitle'];

$registration->opportunity->registerRegistrationMetadata();
$pdf = new RegistrationPdf($registration);
$attachments = $pdf->attachmentsIndex();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 2cm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 250px;
            max-height: 80px;
        }
        .logo-container .site-name {
            font-size: 24pt;
            font-weight: bold;
            color: #222;
            margin: 10px 0;
        }
        .header { margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18pt; color: #222; }
        .info-block { margin: 20px 0; }
        .info-block h2 { font-size: 14pt; color: #444; margin: 15px 0 10px; page-break-after: avoid; }
        .section-title { font-size: 12pt; color: #555; margin: 14px 0 6px; padding-bottom: 3px; border-bottom: 1px solid #ddd; page-break-after: avoid; }
        .field { margin: 10px 0; padding: 10px; background: #f9f9f9; page-break-inside: avoid; }
        .field-label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        .field-value { color: #333; }
        .attachment-name { color: #777; font-size: 9pt; }
        .photos { font-size: 0; }
        .photo { display: inline-block; width: 5cm; margin: 0 8px 8px 0; font-size: 10pt; vertical-align: top; }
        .photo img { width: 5cm; }
        .links { margin: 0; padding-left: 16px; }
        .attachments-index { page-break-inside: avoid; }
        .attachments-index ol { margin: 0; padding-left: 20px; }
        .attachments-index .place { color: #777; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #e9e9e9; font-weight: bold; }
    </style>
</head>
<body>

<?php $this->applyTemplateHook('registration-pdf', 'before'); ?>

<!-- Logo da plataforma -->
<?php $this->applyTemplateHook('registration-pdf-logo', 'before'); ?>
<div class="logo-container">
    <?php if ($logoUrl && file_exists($logoUrl)): ?>
        <?php
        $imageData = base64_encode(file_get_contents($logoUrl));
        $mimeType = mime_content_type($logoUrl);
        ?>
        <img src="data:<?= $mimeType ?>;base64,<?= $imageData ?>" alt="Logo">
    <?php else: ?>
        <div class="site-name"><?= htmlspecialchars($siteName) ?></div>
    <?php endif; ?>
</div>
<?php $this->applyTemplateHook('registration-pdf-logo', 'after'); ?>

<!-- Cabeçalho -->
<?php $this->applyTemplateHook('registration-pdf-header', 'before'); ?>
<div class="header">
    <h1>Ficha de Inscrição #<?= htmlspecialchars($registration->number) ?></h1>
    <p><strong>Oportunidade:</strong> <?= htmlspecialchars($registration->opportunity->name) ?></p>
    <p><strong>Responsável:</strong> <?= htmlspecialchars($registration->owner->name) ?></p>
    <?php if ($registration->sentTimestamp): ?>
        <p><strong>Data de envio:</strong> <?= $registration->sentTimestamp->format('d/m/Y H:i') ?></p>
    <?php endif; ?>
</div>
<?php $this->applyTemplateHook('registration-pdf-header', 'after'); ?>

<!-- Informações básicas -->
<?php $this->applyTemplateHook('registration-pdf-basic-info', 'before'); ?>
<?php if ($registration->category || $registration->range): ?>
<div class="info-block">
    <h2>Informações Básicas</h2>

    <?php if ($registration->category): ?>
        <div class="field">
            <span class="field-label">Categoria:</span>
            <span class="field-value"><?= htmlspecialchars($registration->category) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($registration->range): ?>
        <div class="field">
            <span class="field-label">Faixa/Linha:</span>
            <span class="field-value"><?= htmlspecialchars($registration->range) ?></span>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $this->applyTemplateHook('registration-pdf-basic-info', 'after'); ?>

<!-- Campos personalizados, por etapa -->
<?php $this->applyTemplateHook('registration-pdf-fields', 'before'); ?>
<?php foreach ($pdf->steps() as $number => $step): ?>
    <div class="info-block">
        <?php if ($step['name']): ?>
            <h2><?= $number + 1 ?>. <?= htmlspecialchars($step['name']) ?></h2>
        <?php endif; ?>

        <?php foreach ($step['sections'] as $section): ?>
            <?php if ($section['title']): ?>
                <div class="section-title"><?= htmlspecialchars($section['title']) ?></div>
            <?php endif; ?>

            <?php foreach ($section['items'] as $item): ?>
                <div class="field">
                    <span class="field-label"><?= htmlspecialchars($item['label']) ?></span>
                    <div class="field-value"><?= $item['html'] ?></div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php if ($attachments): ?>
    <div class="info-block attachments-index">
        <h2><?php i::_e('Anexos no fim deste documento') ?></h2>
        <ol>
            <?php foreach ($attachments as $attachment): ?>
                <li value="<?= $attachment['number'] ?>">
                    <?= htmlspecialchars($attachment['title']) ?>
                    <?php if ($attachment['place']): ?>
                        <span class="place">(<?= htmlspecialchars($attachment['place']) ?>)</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
<?php endif; ?>
<?php $this->applyTemplateHook('registration-pdf-fields', 'after'); ?>

<!-- Agentes relacionados -->
<?php if (count($registration->relatedAgents) > 0): ?>
    <?php $this->applyTemplateHook('registration-pdf-agents', 'before'); ?>
    <div class="info-block">
        <h2>Agentes Relacionados</h2>

        <?php foreach ($registration->relatedAgents as $group => $agents): ?>
            <?php foreach ($agents as $agent): ?>
                <div class="field">
                    <span class="field-label"><?= htmlspecialchars($group) ?>:</span>
                    <span class="field-value"><?= htmlspecialchars($agent->name) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php $this->applyTemplateHook('registration-pdf-agents', 'after'); ?>
<?php endif; ?>

<?php $this->applyTemplateHook('registration-pdf', 'after'); ?>

</body>
</html>
