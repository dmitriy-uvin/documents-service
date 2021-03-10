import * as getterTypes from '../store/modules/auth/types/getters';
import { mapGetters } from 'vuex';

export default {
    data: () => ({
       roleValues: {
           worker: 1,
           manager: 2,
           administrator: 3,
           developer: 4
       },
        workerAlias: 'worker',
        developerAlias: 'developer',
        administratorAlias: 'administrator',
        managerAlias: 'manager'
    }),
    computed: {
        ...mapGetters('auth', {
            authUser: getterTypes.GET_USER_DATA
        }),
        developerOrAdministrator() {
            return this.authUser.role[0].alias === this.developerAlias
            ||
            this.authUser.role[0].alias === this.administratorAlias;
        },
        canAddNewDocumentType() {
            return this.isDeveloper;
        },
        isDeveloper() {
            return this.authUser.role[0].alias === this.developerAlias;
        },
        isWorker() {
            return this.authUser.role[0].alias === this.workerAlias;
        },
        adminOrMore() {
            return this.roleValues[this.authUser.role[0].alias] >= this.roleValues.administrator;
        }
    },
    methods: {
        canDeleteUser(userRole) {
            return this.roleValues[this.authUser.role[0].alias] > this.roleValues[userRole];
        }
    }
}
