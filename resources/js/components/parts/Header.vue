<template>
    <b-navbar class="container" v-if="isLoggedIn">
        <template #end>
            <UserDropdown></UserDropdown>
        </template>
    </b-navbar>
</template>

<script>
import * as getterTypes from '../../store/modules/auth/types/getters';
import { mapGetters } from 'vuex';
import UserDropdown from "./UserDropdown";
import * as authMutations from '../../store/modules/auth/types/mutations';
export default {
    name: "Header",
    components: {
        UserDropdown
    },
    props: ['authData'],
    beforeMount() {
        this.saveUserData();
    },
    methods: {
        saveUserData() {
            this.$store.commit('auth/' + authMutations.SET_USER_DATA, this.authData);
            this.$store.commit('auth/' + authMutations.LOG_IN);
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
