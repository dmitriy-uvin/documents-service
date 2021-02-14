<template>
    <DefaultLayout>
        <template v-slot:title>
            Физические лица
        </template>
        <template v-slot:content>
            <b-loading :is-full-page="true" v-model="isLoading"></b-loading>
            <section>
                <b-table
                    v-if="users.length && !isWorker"
                    :data="users"
                    :bordered="false"
                    :hoverable="true"
                    :mobile-cards="true"
                    sort-icon="arrow-up"
                    :sort-icon-size="'is-small'"
                    detailed
                    detail-key="id"
                    :show-detail-icon="true"
                >
                    <b-table-column
                        field="id"
                        label="ID"
                        width="40"
                        numeric
                        v-slot="props"
                        sortable
                    >
                        {{ props.row.id }}
                    </b-table-column>

                    <b-table-column
                        field="id"
                        label="ФИО"
                        v-slot="props"
                    >
                        <a :href="`/individuals/${props.row.id}`">{{ getFullName(props.row) }}</a>
                    </b-table-column>

                    <b-table-column
                        field="created_at"
                        label="Дата создания"
                        v-slot="props"
                        sortable
                    >
                            <span class="tag is-info">
                                {{ createdAt(props.row.created_at) }}
                            </span>
                    </b-table-column>

                    <template #footer>
                        <div class="has-text-right">
                            Физических лиц: {{ users.length }}
                        </div>
                    </template>
                </b-table>
                <b-message type="is-danger" v-else-if="isWorker">
                    Вы не можете просмотреть список существующих физических лиц.
                    Вы можете использовать поиск, чтобы найти нужное Вам лицо!
                </b-message>
                <b-message type="is-danger" v-else-if="!users.length">
                    Не найдено ни одного физического лица!
                </b-message>
            </section>
        </template>
    </DefaultLayout>
</template>

<script>
import roleMixin from "../../mixins/roleMixin";
import DefaultLayout from "../layouts/DefaultLayout";
import datetimeMixin from "../../mixins/datetimeMixin";
import individualsMixin from "../../mixins/individualsMixin";
import individualService from "../../services/individual/individualService";
export default {
    name: "PhysicalList",
    mixins: [roleMixin, datetimeMixin, individualsMixin],
    components: {
        DefaultLayout
    },
    data: () => ({
       users: [],
        isLoading: true
    }),
    async mounted() {
        this.users = await individualService.getIndividualUsers();
        this.isLoading = false;
        console.log(this.users);
    }
}
</script>

<style scoped>

</style>
