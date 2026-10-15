app.component('panel--meus-cadastros', {
    template: $TEMPLATES['panel--meus-cadastros'],

    setup() {
        // os textos estão localizados no arquivo texts.php deste componente
        const text = Utils.getTexts('panel--meus-cadastros')
        return { text }
    },

    data() {
        return {
            loading: true,
            registrations: [],
        }
    },

    async created() {
        const { opportunityId, subsiteId } = $MAPAS.config.panelMeusCadastros ?? {};

        if (!opportunityId) {
            this.loading = false;
            return;
        }

        const query = {
            '@select': 'id,number,status,singleUrl,opportunity.{name},sentTimestamp,updateTimestamp,editableFields,editSentTimestamp',
            '@order': 'updateTimestamp DESC',
            // `view` esconderia o rascunho do próprio dono: o cache de permissão não o guarda.
            '@permissions': '@control',
            'opportunity': `EQ(${opportunityId})`,
            'status': 'GTE(0)',
            'user': 'EQ(@me)',
        };

        // Checagem dupla, proposital: o edital diz o quê, o subsite diz de onde.
        if (subsiteId) {
            query._subsiteId = `EQ(${subsiteId})`;
        }

        try {
            this.registrations = await new API('registration').find(query);
        } finally {
            this.loading = false;
        }
    },

    methods: {
        title(registration) {
            return registration.opportunity?.name || `${this.text('inscrição')} ${registration.number}`;
        },

        // Devolvido pela moderação: segue no status 1, com campos reabertos e a correção ainda não reenviada.
        pending(registration) {
            return registration.status == 1 && registration.editableFields?.length > 0 && !registration.editSentTimestamp;
        },

        // O nome do status configurado na oportunidade; sem ele, o texto padrão.
        statusName(registration) {
            if (this.pending(registration)) {
                return this.text('pendente de moderação');
            }

            const labels = $MAPAS.config.panelMeusCadastros?.statusLabels ?? {};

            return labels[registration.status] ?? (registration.status == 0 ? this.text('não enviada') : this.text('enviada'));
        },

        meta(registration) {
            const enviada = registration.status != 0 && registration.sentTimestamp;
            const data = enviada ? registration.sentTimestamp : registration.updateTimestamp;

            if (!data) {
                return '';
            }

            const rotulo = enviada ? this.text('enviado em') : this.text('última edição em');

            return `${rotulo} ${data.date('2-digit year')}`;
        },
    },
});
