<template>
    <b-navbar class="container" v-if="isLoggedIn">

        <template #start>
            <b-navbar-item href="/documents" has-link :active="isActiveItem('/documents')">
                <b-icon icon="file" class="mr-1"></b-icon>
                Загрузка документов
            </b-navbar-item>
            <b-navbar-item href="/users" has-link :active="isActiveItem('/users')">
                <b-icon icon="users" class="mr-1"></b-icon>
                Пользователи
            </b-navbar-item>
            <b-navbar-item href="#">
                Физические лица
            </b-navbar-item>
            <b-navbar-item
                href="/editor"
                v-if="developerOrAdministrator"
                :active="isActiveItem('/editor')"
            >
                <b-icon icon="wrench" class="mr-1"></b-icon>
                Редактор
            </b-navbar-item>
        </template>

        <template #end>
            <b-dropdown
                position="is-bottom-left"
                append-to-body
                aria-role="menu"
            >
                <template #trigger>
                    <a
                        class="navbar-item"
                        role="button"
                    >
                        <span>{{ authData.first_name }}</span>
                        <b-icon icon="chevron-down"></b-icon>
                    </a>
                </template>
                <b-dropdown-item has-link>
                    <a href="/home">
                        <b-icon icon="user"></b-icon>
                        Профиль
                    </a>
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
import roleMixin from "../../mixins/roleMixin";
export default {
    name: "Header",
    data: () => ({
    }),
    mixins: [roleMixin],
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
        },
        isActiveItem(pathName) {
            return pathName === window.location.pathname;
        }
    },
    computed: {
        ...mapGetters('auth', {
            isLoggedIn: getterTypes.IS_LOGGED_IN
        }),
    }
}
</script>

<style scoped>

</style>
