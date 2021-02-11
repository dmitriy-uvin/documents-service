<template>
    <DefaultLayout>
        <template v-slot:title>
            Физические лица
        </template>
        <template v-slot:content>
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
                    @details-open="(row) => $buefy.toast.open(`Expanded ${row.user.first_name}`)"
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
                        label="Роль"
                        width="40"
                        v-slot="props"
                    >
                        <b-tag else :class="'is-' + props.row.role[0].alias">
                            {{ props.row.role[0].name }}
                        </b-tag>
                    </b-table-column>

                    <b-table-column
                        field="first_name"
                        label="Имя"
                        v-slot="props"
                        sortable
                    >
                        {{ props.row.first_name }}
                    </b-table-column>

                    <b-table-column
                        field="last_name"
                        label="Фамилия"
                        v-slot="props"
                        sortable
                    >
                        {{ props.row.second_name }}
                    </b-table-column>

                    <b-table-column
                        field="last_name"
                        label="Отчество"
                        v-slot="props"
                        sortable
                    >
                        {{ props.row.patronymic }}
                    </b-table-column>

                    <b-table-column
                        field="date"
                        label="Создан"
                        centered
                        v-slot="props"
                        sortable
                    >
                            <span class="tag is-info">
                                {{ new Date(props.row.created_at).toLocaleDateString() }}
                            </span>
                    </b-table-column>

                    <b-table-column
                        label="Отдел"
                        v-slot="props"
                        sortable
                    >
                            <span>
                                {{ props.row.department }}
                            </span>
                    </b-table-column>

                    <b-table-column v-slot="props" label="Статус">
                        <b-tag type="is-danger" v-if="props.row.is_blocked" class="cursor-pointer">
                            <b-icon icon="times-circle" class="mr-1"></b-icon>Blocked
                        </b-tag>
                        <b-tag type="is-success" v-else class="cursor-pointer">
                            <b-icon icon="check-circle" class="mr-1"></b-icon>Active
                        </b-tag>
                    </b-table-column>

                    <template #footer>
                        <div class="has-text-right">
                            Пользователей: {{ users.length }}
                        </div>
                    </template>
                </b-table>
                <b-message type="is-danger" v-else-if="isWorker">
                    Вы не можете просмотреть список существующих физических лиц.
                    Вы можете использовать поиск, чтобы найти нужное Вам лицо!
                </b-message>
                <b-message type="is-danger" v-else>
                    Не найдено ни одного физического лица!
                </b-message>
            </section>
        </template>
    </DefaultLayout>
</template>

<script>
import userService from "../../services/user/userService";
import roleMixin from "../../mixins/roleMixin";
import DefaultLayout from "../layouts/DefaultLayout";
export default {
    name: "PhysicalList",
    mixins: [roleMixin],
    components: {
        DefaultLayout
    },
    data: () => ({
       users: []
    }),
    async mounted() {
        this.users = await userService.getIndividualUsers();
    }
}
</script>

<style scoped>

</style>
