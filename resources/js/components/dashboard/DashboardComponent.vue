<template>
    <div class="card">
        <div class="card-header d-block">
            <h2 class="title is-4 text-center py-3">
                {{ user.first_name + ' ' + user.second_name + ' ' + user.patronymic }}
            </h2>
            <h2 class="subtitle text-center text-primary">{{ user.role[0].name }}</h2>
        </div>
        <div class="card-content">
            <div class="content">

            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DashboardComponent",
    props: ['user'],
    data: () => ({
        newUser: {
            first_name: '',
            second_name: '',
            patronymic: '',
            email: '',
            password: '',
            role: ''
        },
        newDocument: {
            name: '',
            alias: '',
            type: ''
        }
    })
}
</script>

<style scoped>

</style>
