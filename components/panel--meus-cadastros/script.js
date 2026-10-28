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
            festivities: [],
            festivityFormUrl: $MAPAS.config.panelMeusCadastros?.festivityFormUrl,
        }
    },

    async created() {
        try {
            [this.registrations, this.festivities] = await Promise.all([this.findRegistrations(), this.findFestivities()]);
        } finally {
            this.loading = false;
        }
    },

    methods: {
        findRegistrations() {
            const { opportunityId, subsiteId } = $MAPAS.config.panelMeusCadastros ?? {};

            if (!opportunityId) {
                return [];
            }

            const query = {
                '@select': 'id,number,status,singleUrl,opportunity.{name},sentTimestamp,updateTimestamp,editableFields,editSentTimestamp',
                '@order': 'updateTimestamp DESC',
                // traz também o rascunho do próprio dono
                '@permissions': '@control',
                'opportunity': `EQ(${opportunityId})`,
                'status': 'GTE(0)',
                'user': 'EQ(@me)',
            };

            // Filtra também pelo subsite.
            if (subsiteId) {
                query._subsiteId = `EQ(${subsiteId})`;
            }

            return new API('registration').find(query);
        },

        // Os festejos de quem cadastra, rascunhos e publicados.
        findFestivities() {
            if (!this.festivityFormUrl) {
                return [];
            }

            return new API('event').find({
                '@select': 'id,name,status,singleUrl,createTimestamp,updateTimestamp',
                '@order': 'updateTimestamp DESC',
                // traz também o rascunho do próprio dono
                '@permissions': '@control',
                'status': 'GTE(0)',
                'user': 'EQ(@me)',
            });
        },

        festivityUrl(festivity) {
            return `${this.festivityFormUrl}${festivity.id}/`;
        },

        festivityMeta(festivity) {
            const data = festivity.updateTimestamp || festivity.createTimestamp;

            return [this.text('festejo popular'), data ? `${this.text('última edição em')} ${data.date('2-digit year')}` : ''].filter(Boolean).join(' · ');
        },

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
