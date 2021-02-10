<template>
    <div class="card">
        <div class="card-header d-block">
            <h2 class="title is-4 text-center py-3">
                Пользователи
            </h2>
        </div>
        <div class="card-content">
            <div class="content">
                <section>
                    <b-table
                        :data="users"
                        :bordered="false"
                        :hoverable="true"
                        :mobile-cards="true"
                        sort-icon="arrow-up"
                        :sort-icon-size="'is-small'"
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
                            <b-tag type="is-warning" else>
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
                        <b-table-column v-slot="props" label="Действия">
                            <b-button type="is-warning" size="is-small"
                                      icon-right="lock" />
                            <b-button type="is-danger" size="is-small"
                                      icon-right="trash" />
                            <b-tooltip label="Просмотреть пользователя"
                                       type="is-dark"
                                       position="is-top">
                                <b-button type="is-info" size="is-small"
                                          icon-right="eye" @click="goToUser(props.row.id)"
                                />
                            </b-tooltip>
                        </b-table-column>

                        <b-table-column v-slot="props" label="Статус">
                            <b-tag type="is-danger" v-if="props.row.is_blocked" class="cursor-pointer">
                                <b-icon icon="times-circle" class="mr-1"></b-icon>Blocked
                            </b-tag>
                            <b-tag type="is-success" else class="cursor-pointer">
                                <b-icon icon="check-circle" class="mr-1"></b-icon>Active
                            </b-tag>
                        </b-table-column>

                        <template #footer>
                            <div class="has-text-right">
                                Пользователей: {{ users.length }}
                            </div>
                        </template>
                    </b-table>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "UsersList",
    props: ['users'],
    data:() => ({

    }),
    methods: {
        goToUser(id) {
            window.location.href = '/users/' + id;
        },
    },
    computed: {

    }
}
</script>

<style scoped>
.blocked {

}
</style>
