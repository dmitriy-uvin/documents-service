<template>
    <DefaultLayout>
        <template v-slot:title>
            {{ user.second_name + ' ' + user.first_name + ' ' + user.patronymic }}
        </template>
        <template v-slot:subtitle>
            {{ user.role[0].name }}
        </template>
        <template v-slot:content>
            <div class="row" v-if="!isWorker">
                <div class="col-md-4">
                    <b-field label="API KEY">
                        <b-input v-model="apiKey" readonly></b-input>
                    </b-field>

                    <b-button
                        type="is-info"
                        v-if="!apiKey"
                        :loading="loading"
                        @click="generateApiKey"
                    >
                        Сгенерировать
                    </b-button>
                </div>
            </div>
            <b-message type="is-warning" v-else>В разработке!</b-message>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
import EventBus from "../../events/eventBus";
import userService from "../../services/user/userService";
import roleMixin from "../../mixins/roleMixin";
export default {
    name: "DashboardComponent",
    props: ['user'],
    components: {
        DefaultLayout
    },
    mixins: [roleMixin],
    data: () => ({
        apiKey: '',
        loading: false
    }),
    methods: {
        async generateApiKey() {
            try {
                this.loading = true;
                const apiKey = this.getKey();
                const response = await userService.updateApiKey({
                    api_key: apiKey
                });
                this.loading = false;
                console.log(response);
                console.log(response.api_key);
                this.apiKey = response.api_key;
            } catch (error) {
                EventBus.$emit('error', error.message);
                this.loading = false;
            }
        },
        getKey() {
            return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        },
        setKey() {
            this.apiKey = this.authUser.api_key;
        }
    },
    mounted() {
        this.setKey();
    }
}
</script>

<style scoped>

</style>
