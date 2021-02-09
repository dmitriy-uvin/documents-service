<template>
    <b-navbar class="container" v-if="isLoggedIn">
        <template #brand>
            <b-navbar-item tag="router-link" :to="{ path: '/' }">
                <img
                    src="https://raw.githubusercontent.com/buefy/buefy/dev/static/img/buefy-logo.png"
                    alt="Lightweight UI components for Vue.js based on Bulma"
                >
            </b-navbar-item>
        </template>
        <template #start>
            <b-navbar-item href="#">
                Загрузка документов
            </b-navbar-item>
            <b-navbar-item href="#">
                Пользователи
            </b-navbar-item>
            <b-navbar-item href="#">
                Физические лица
            </b-navbar-item>
        </template>

        <template #end>
            <b-dropdown
                v-model="navigationOpen"
                position="is-bottom-left"
            >
                <template #trigger>
                    <a
                        class="navbar-item"
                        role="button"
                    >
                        <span>{{ authData.name }}</span>
                        <b-icon icon="chevron-down"></b-icon>
                    </a>
                </template>
                <b-dropdown-item has-link>
                    <a href="https://google.com" target="_blank">
                        <b-icon icon="link"></b-icon>
                        Google (link)
                    </a>
                </b-dropdown-item>
                <hr class="dropdown-divider">
                <b-dropdown-item>
                    <b-icon icon="user"></b-icon>
                    Профиль
                </b-dropdown-item>
                <b-dropdown-item @click="logout">
                    <b-icon icon="times"></b-icon>
                    Выйти
                </b-dropdown-item>
            </b-dropdown>
        </template>
    </b-navbar>
</template>

<script>
import * as getterTypes from '../../store/modules/auth/types/getters';
import { mapGetters, mapActions } from 'vuex';
import * as authMutations from '../../store/modules/auth/types/mutations';
import * as actionTypes from '../../store/modules/auth/types/actions';
export default {
    name: "Header",
    data: () => ({
        navigationOpen: false
    }),
    props: ['authData'],
    beforeMount() {
        this.saveUserData();
    },
    methods: {
        saveUserData() {
            this.$store.commit('auth/' + authMutations.SET_USER_DATA, this.authData);
            this.$store.commit('auth/' + authMutations.LOG_IN);
        },
        ...mapActions('auth', {
            logoutAction: actionTypes.LOGOUT
        }),
        async logout() {
            try  {
                await this.logoutAction();
                window.location.href = '';
            } catch (error) {
                console.log(error);
            }
        }
    },
    computed: {
        ...mapGetters('auth', {
            isLoggedIn: getterTypes.IS_LOGGED_IN
        })
    }
}
</script>

<style scoped>

</style>
