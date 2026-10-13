<?php

namespace MestresDaCultura;

use MapasCulturais\App;
use MapasCulturais\Entities\File;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationFieldConfiguration;
use MapasCulturais\Entities\RegistrationFileConfiguration;
use MapasCulturais\i;
use MapasCulturais\Modules\Opportunities\RegistrationPdfFormatter;

/**
 * Conteúdo do PDF da ficha (views/registration/pdf.php): os campos por etapa e seção, na ordem do formulário,
 * com os valores escritos como na ficha, e os anexos numerados na ordem em que são juntados ao fim do arquivo.
 */
class RegistrationPdf
{
    // Recorte 4:3 de 800x600, nítido no tamanho impresso.
    private const PHOTO_TRANSFORMATION = 'galleryFull';

    /** @var array<int,int> id do arquivo => número do anexo */
    private array $attachmentNumbers;

    function __construct(private Registration $registration)
    {
        $this->attachmentNumbers = [];

        foreach ($this->mergedAttachments() as $index => $file) {
            $this->attachmentNumbers[$file->id] = $index + 1;
        }
    }

    /**
     * Etapas e seções com ao menos um campo respondido.
     *
     * @return array<array{name: string, sections: array<array{title: ?string, items: array<array{label: string, html: string}>}>}>
     */
    function steps(): array
    {
        $steps = [];

        foreach ($this->orderedConfigurations() as $config) {
            if (!$this->isVisible($config)) {
                continue;
            }

            $step = $config->step?->id ?? 0;
            $steps[$step] ??= ['name' => $config->step?->name ?? '', 'sections' => [['title' => null, 'items' => []]]];

            if ($this->isSection($config)) {
                $steps[$step]['sections'][] = ['title' => $config->title, 'items' => []];
                continue;
            }

            $html = $config instanceof RegistrationFileConfiguration ? $this->filesHtml($config) : $this->valueHtml($config);

            if ($html !== '') {
                $steps[$step]['sections'][array_key_last($steps[$step]['sections'])]['items'][] = ['label' => $this->label($config), 'html' => $html];
            }
        }

        foreach ($steps as &$step) {
            $step['sections'] = array_values(array_filter($step['sections'], fn ($section) => $section['items']));
        }

        return array_values(array_filter($steps, fn ($step) => $step['sections']));
    }

    /**
     * Um por anexo juntado ao fim do PDF, na mesma ordem, com a pergunta e o lugar dela no formulário.
     *
     * @return array<array{number: int, title: string, place: string}>
     */
    function attachmentsIndex(): array
    {
        $configs = [];

        foreach ($this->registration->opportunity->registrationFileConfigurations as $config) {
            $configs[$config->fileGroupName] = $config;
        }

        $places = $this->places();
        $index = [];

        foreach ($this->mergedAttachments() as $file) {
            $config = $configs[$file->group] ?? null;

            $index[] = [
                'number' => $this->attachmentNumbers[$file->id],
                'title' => $config ? rtrim($config->title, ' :') : $file->name,
                'place' => $config ? ($places[$config->fileGroupName] ?? '') : '',
            ];
        }

        return $index;
    }

    /**
     * Os anexos em PDF juntados ao fim do arquivo, na ordem de Controllers\Registration::collectPDFAttachments.
     *
     * @return File[]
     */
    private function mergedAttachments(): array
    {
        $attachments = [];

        foreach ($this->registration->files as $files) {
            foreach (is_array($files) ? $files : [$files] as $file) {
                if ($file->mimeType === 'application/pdf' && file_exists($file->path)) {
                    $attachments[] = $file;
                }
            }
        }

        return $attachments;
    }

    /** @return array<RegistrationFieldConfiguration|RegistrationFileConfiguration> na ordem do formulário: etapa, depois posição */
    private function orderedConfigurations(): array
    {
        $opportunity = $this->registration->opportunity;
        $configs = array_merge($opportunity->registrationFieldConfigurations, $opportunity->registrationFileConfigurations);

        usort($configs, fn ($a, $b) => [$a->step?->displayOrder, $a->displayOrder] <=> [$b->step?->displayOrder, $b->displayOrder]);

        return $configs;
    }

    /** @return array<string,string> grupo do arquivo => "Etapa › Seção" */
    private function places(): array
    {
        $places = [];
        $section = null;
        $step = null;

        foreach ($this->orderedConfigurations() as $config) {
            if ($config->step?->id !== $step) {
                $step = $config->step?->id;
                $section = null;
            }

            if ($this->isSection($config)) {
                $section = $config->title;
            } elseif ($config instanceof RegistrationFileConfiguration) {
                $places[$config->fileGroupName] = implode(' › ', array_filter([$config->step?->name, $section]));
            }
        }

        return $places;
    }

    /**
     * Diz se o campo apareceu no formulário, inclusive o que depende do valor "0" de uma caixa desmarcada.
     */
    private function isVisible(RegistrationFieldConfiguration|RegistrationFileConfiguration $config): bool
    {
        $registration = $this->registration;

        if ($registration->isFieldVisisble($config)) {
            return true;
        }

        $parent = null;

        foreach ($registration->opportunity->registrationFieldConfigurations as $field) {
            if ($config->conditional && $field->fieldName === $config->conditionalField) {
                $parent = $field;
            }
        }

        return $parent?->fieldType === 'checkbox'
            && (string) $config->conditionalValue === '0'
            && $registration->{$parent->fieldName} === ''
            && $registration->isFieldVisisble($parent);
    }

    private function isSection(RegistrationFieldConfiguration|RegistrationFileConfiguration $config): bool
    {
        return $config instanceof RegistrationFieldConfiguration && $config->fieldType === 'section';
    }

    // Termina o título com ":", salvo quando já termina em pontuação.
    private function label(RegistrationFieldConfiguration|RegistrationFileConfiguration $config): string
    {
        $title = rtrim($config->title, ' :');

        return ($config->required ? '* ' : '') . (preg_match('/[?.!]$/u', $title) ? $title : "{$title}:");
    }

    private function valueHtml(RegistrationFieldConfiguration $config): string
    {
        $value = $this->registration->{$config->fieldName} ?? null;
        $entity_field = $config->config['entityField'] ?? null;

        // A caixa desmarcada, gravada como '', sai como "Não".
        if ($config->fieldType === 'checkbox' && $value !== null) {
            return $this->escape(filter_var($value, FILTER_VALIDATE_BOOLEAN) ? i::__('Sim') : i::__('Não'));
        }

        if ($value === null || $value === '' || $value === []) {
            return '';
        }

        return match (true) {
            $config->fieldType === 'date', $entity_field === 'dataDeNascimento' => $this->escape($this->date((string) $value)),
            $entity_field === '@gallery' => $this->photosHtml((array) $value),
            $entity_field === '@videos', $entity_field === '@links' => $this->linksHtml((array) $value),
            default => RegistrationPdfFormatter::formatFieldValueAsHtml($config, $value),
        };
    }

    private function date(string $value): string
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', substr($value, 0, 10));

        return $date ? $date->format('d/m/Y') : $value;
    }

    // As fotos entram no PDF; sem o arquivo no disco, fica o nome.
    private function photosHtml(array $photos): string
    {
        $html = '';

        foreach ($photos as $photo) {
            $photo = (array) $photo;
            $file = App::i()->repo('File')->find($photo['id'] ?? 0);
            $image = $file?->transform(self::PHOTO_TRANSFORMATION);
            $caption = $this->escape((string) (($photo['description'] ?? null) ?: ($photo['name'] ?? '')));

            if ($image && file_exists($image->path)) {
                $data = base64_encode(file_get_contents($image->path));
                $html .= "<div class=\"photo\"><img src=\"data:{$image->mimeType};base64,{$data}\" alt=\"{$caption}\"></div>";
            } elseif ($caption !== '') {
                $html .= "<div class=\"photo\">{$caption}</div>";
            }
        }

        return $html === '' ? '' : "<div class=\"photos\">{$html}</div>";
    }

    // Vídeos e links, um por linha, com o título quando houver.
    private function linksHtml(array $links): string
    {
        $html = '';

        foreach ($links as $link) {
            $link = (array) $link;
            $url = trim((string) ($link['value'] ?? ''));
            $title = trim((string) ($link['title'] ?? ''));

            if ($url !== '') {
                $html .= '<li>' . ($title === '' ? '' : '<strong>' . $this->escape($title) . ':</strong> ') . $this->escape($url) . '</li>';
            }
        }

        return $html === '' ? '' : "<ul class=\"links\">{$html}</ul>";
    }

    // Cada arquivo com o número do anexo no fim do PDF; o que não foi juntado aponta para o ZIP.
    private function filesHtml(RegistrationFileConfiguration $config): string
    {
        $files = $this->registration->files[$config->fileGroupName] ?? [];
        $html = '';

        foreach (is_array($files) ? $files : [$files] as $file) {
            $number = $this->attachmentNumbers[$file->id] ?? null;
            $name = $this->escape($file->description ?: $file->name);

            $html .= '<div class="attachment">▸ ' . ($number
                ? $this->escape(sprintf(i::__('Anexo %s, no fim deste documento'), $number)) . " <span class=\"attachment-name\">({$name})</span>"
                : $name . ' <span class="attachment-name">(' . $this->escape(i::__('não incluído neste PDF; está no ZIP de anexos')) . ')</span>')
                . '</div>';
        }

        return $html;
    }

    private function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }
}
