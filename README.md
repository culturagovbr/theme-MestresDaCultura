# theme-MestresDaCultura

Tema da Rede Nacional de Mestras e Mestres das Culturas Tradicionais e Populares para o Mapas Culturais.

Roda como subsite, com namespace `MestresDaCultura`, e herda de `BaseV2`. As funcionalidades vivem no plugin `MDC`.

## Sobrescritas do core

- **PDF da ficha da inscrição** (`views/registration/pdf.php` e `RegistrationPdf.php`): cópia da view do módulo Opportunities com os mesmos ganchos. Os campos saem por etapa e seção, na ordem do formulário, com datas, caixas de marcar, fotos e links escritos como na ficha, e os anexos juntados ao fim são numerados e listados com a pergunta de origem. Ao atualizar o core, comparar com `modules/Opportunities/views/registration/pdf.php`.
