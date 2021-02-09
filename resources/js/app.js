require('./bootstrap');

import Vue from 'vue';
import Buefy from 'buefy'
import 'buefy/dist/buefy.css'
import store from './store';
import Vuelidate from 'vuelidate';


Vue.use(Buefy, {
    defaultIconPack: 'fa'
});
Vue.use(Vuelidate);

Vue.component('dashboard-component', require('./components/dashboard/DashboardComponent.vue').default);
Vue.component('header-component', require('./components/parts/Header.vue').default);

// Auth Components
Vue.component('login-component', require('./components/auth/Login.vue').default);


const app = new Vue({
    el: '#app',
    store
});
