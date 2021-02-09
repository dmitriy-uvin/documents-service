<template>
    <div class="card">
        <div class="card-header d-block">
            <h2 class="title is-4 text-center py-3">
                {{ user.name }}
            </h2>
            <h2 class="subtitle text-center text-primary">{{ user.role[0].name }}</h2>
        </div>
        <div class="card-content">
            <div class="content">
                <h2 class="title is-5">Создание пользователя</h2>
                <div class="row">
                    <div class="col-md-3">
                        <b-field label="Имя">
                            <b-input v-model="newUser.name"></b-input>
                        </b-field>
                    </div>
                    <div class="col-md-3">
                        <b-field label="E-mail">
<!--                                 type="is-danger"-->
<!--                                 message="This email is invalid"-->
                            <b-input type="email" v-model="newUser.email"></b-input>
                        </b-field>
                    </div>
                    <div class="col-md-3">
                        <b-field label="Пароль">
                            <b-input type="password"
                                     v-model="newUser.password"
                            >
                            </b-input>
                        </b-field>
                    </div>
                </div>
                <h2 class="title is-5">Добавление типа документов</h2>
                <div class="row">
                    <div class="col-md-3">
                        <b-field label="Название">
                            <b-input v-model="newDocument.name"></b-input>
                        </b-field>
                    </div>
                    <div class="col-md-3">
                        <b-field label="Alias">
                            <b-input v-model="newDocument.alias"></b-input>
                        </b-field>
                    </div>
                    <div class="col-md-3">
                        <b-field label="Тип">
                            <b-input v-model="newDocument.type"></b-input>
                        </b-field>
                    </div>
                </div>
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
            name: '',
            email: '',
            password: '',
            role: ''
        },
        newDocument: {
            name: '',
            alias: '',
            type: ''
        }
    }),
    watch: {
        'newDocument.name': function (val, oldVal) {
            this.newDocument.alias = 123;
            console.log(this.newDocument);
        }
    }
}
</script>

<style scoped>

</style>
