<template>
    <div class="card">
        <div class="card-header d-block">
            <h2 class="title is-4 text-center py-3">
                Редактор
            </h2>
        </div>
        <div class="card-content">
            <div class="content">
                <b-tabs v-model="activeTab">
                    <b-tab-item label="Пользователи">
                        <h2>Создать руководителя</h2>
                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <b-field label="Имя"
                                         :type="firstNameErrors.length ? 'is-danger' : ''"
                                         :message="!firstNameErrors.length ? '' : firstNameErrors[0]"
                                >
                                    <b-input v-model="newUser.first_name"></b-input>
                                </b-field>
                            </div>
                            <div class="col-md-3 mt-3">
                                <b-field label="Фамилия"
                                         :type="secondNameErrors.length ? 'is-danger' : ''"
                                         :message="!secondNameErrors.length ? '' : secondNameErrors[0]"
                                >
                                    <b-input v-model="newUser.second_name"></b-input>
                                </b-field>
                            </div>
                            <div class="col-md-3 mt-3">
                                <b-field label="Отчество"
                                         :type="patronymicErrors.length ? 'is-danger' : ''"
                                         :message="!patronymicErrors.length ? '' : patronymicErrors[0]"
                                >
                                    <b-input v-model="newUser.patronymic"></b-input>
                                </b-field>
                            </div>
                            <div class="col-md-3 mt-3">
                                <b-field label="Отдел"
                                         :type="departmentErrors.length ? 'is-danger' : ''"
                                         :message="!departmentErrors.length ? '' : departmentErrors[0]"
                                >
                                    <b-input v-model="newUser.department"></b-input>
                                </b-field>
                            </div>
                            <div class="col-md-3 mt-3">
                                <b-field label="E-mail"
                                     :type="emailErrors.length ? 'is-danger' : ''"
                                     :message="!emailErrors.length ? '' : emailErrors[0]"
                                >
                                    <b-input type="email" v-model="newUser.email"></b-input>
                                </b-field>
                            </div>
                            <div class="col-md-3 mt-3">
                                <b-field label="Пароль"
                                    :type="passwordErrors.length ? 'is-danger' : ''"
                                    :message="!passwordErrors.length ? '' : passwordErrors[0]"
                                >
                                    <b-input type="password" password-reveal
                                             v-model="newUser.password"
                                    >
                                    </b-input>
                                </b-field>
                            </div>
                        </div>
                        <div class="buttons mt-3">
                            <b-button
                                type="is-primary"
                                :loading="newUser.loading"
                                @click="addManager"
                            >Создать</b-button>
                        </div>
                    </b-tab-item>

                    <b-tab-item label="Типы документов">
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
                    </b-tab-item>
                </b-tabs>
            </div>
        </div>
    </div>
</template>

<script>
import EventBus from "../../events/eventBus";
import userService from "../../services/user/userService";
import { validationMixin } from 'vuelidate';
import { required, minLength, email } from 'vuelidate/lib/validators';
export default {
    name: "EditorComponent",
    mixins: [validationMixin],
    validations: {
        newUser: {
            first_name: { required },
            second_name: { required },
            patronymic: { required },
            email: { required, email },
            password: { required, minLength: minLength(8) },
            department: { required }
        }
    },
    data: () => ({
        activeTab: 0,
        newUser: {
            first_name: '',
            second_name: '',
            patronymic: '',
            email: '',
            password: '',
            department: '',
            loading: false
        },
        newDocument: {
            name: '',
            alias: '',
            type: ''
        }
    }),
    methods: {
        async addManager() {
            this.$v.$touch();
            if (!this.$v.$invalid) {
                try {
                    this.newUser.loading = true;
                    await userService.addManager(this.newUser);
                    this.newUser.loading = false;
                    this.clearNewUser();
                    this.$v.$reset();
                    EventBus.$emit('manager-added');
                } catch (error) {
                    this.newUser.loading = false;
                    EventBus.$emit('error', error.message);
                }
            }
        },
        clearNewUser() {
            this.newUser = {
                first_name: '',
                second_name: '',
                patronymic: '',
                email: '',
                password: '',
                loading: false
            };
        }
    },
    computed: {
        firstNameErrors() {
            const errors = [];
            if (!this.$v.newUser['first_name'].$dirty) {
                return errors;
            }
            !this.$v.newUser['first_name'].required &&
                errors.push('Имя обязательное поле');
            return errors;
        },
        secondNameErrors() {
            const errors = [];
            if (!this.$v.newUser['second_name'].$dirty) {
                return errors;
            }
            !this.$v.newUser['second_name'].required &&
            errors.push('Фамилия обязательное поле');
            return errors;
        },
        patronymicErrors() {
            const errors = [];
            if (!this.$v.newUser['patronymic'].$dirty) {
                return errors;
            }
            !this.$v.newUser['patronymic'].required &&
                errors.push('Отчество обязательное поле');
            return errors;
        },
        departmentErrors() {
            const errors = [];
            if (!this.$v.newUser['department'].$dirty) {
                return errors;
            }
            !this.$v.newUser['department'].required &&
            errors.push('Отедл обязательное поле');
            return errors;
        },
        emailErrors() {
            const errors = [];
            if (!this.$v.newUser['email'].$dirty) {
                return errors;
            }
            !this.$v.newUser['email'].email &&
            errors.push('Введите корректный E-mail');
            !this.$v.newUser['email'].required &&
            errors.push('E-mail обязательное поле');
            return errors;
        },
        passwordErrors() {
            const errors = [];
            if (!this.$v.newUser['password'].$dirty) {
                return errors;
            }
            !this.$v.newUser['password'].minLength &&
            errors.push('Пароль должен быть длинее 8 символов');
            !this.$v.newUser['password'].required &&
            errors.push('Пароль обязательное поле');
            return errors;
        },
    }
}
</script>

<style scoped>

</style>
