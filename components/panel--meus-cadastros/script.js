app.component('panel--meus-cadastros', {
    template: $TEMPLATES['panel--meus-cadastros'],

    setup() {
        // os textos estão localizados no arquivo texts.php deste componente
        const text = Utils.getTexts('panel--meus-cadastros')
        const messages = useMessages();
        return { text, messages }
    },

    data() {
        return {
            loading: true,
            failed: false,
            registrations: [],
            festivities: $MAPAS.config.panelMeusCadastros?.festivities ?? [],
            moreFestivities: $MAPAS.config.panelMeusCadastros?.moreFestivities ?? false,
        }
    },

    // Mostra o que carregou; na falha da busca, avisa pelo aviso de erro do core.
    async created() {
        try {
            this.registrations = await this.findRegistrations();
        } catch {
            this.failed = true;
        }

        this.loading = false;

        if (this.failed) {
            this.messages.error(this.text('falha ao carregar'));
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

        // Exclui o rascunho, pela regra de exclusão de inscrição do core, e o tira da lista.
        async remove(registration) {
            try {
                registration.disableMessages();
                await registration.delete(true);
                this.registrations = this.registrations.filter((item) => item.id !== registration.id);
                this.messages.success(this.text('rascunho excluído'));
            } catch {
                this.messages.error(this.text('erro ao excluir'));
            }
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
