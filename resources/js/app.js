import axios from "axios";
require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './includes/routes';
import components from './includes/components';
import { store } from './includes/store';
import directives from "./includes/directives";
var main_title = 'پنل مدیریت لیان';

Vue.use(VueRouter, components);

const app = new Vue({
    el: '#lian-app',
    store: store,
    router: new VueRouter(routes),
    components: components,
    directives: directives,
    computed: {
        isLoading() {
            return this.$store.state.is_route_loading;
        },
        asideStatus() {
            return this.$store.state.aside_is_open;
        },
        asideViewable() {
            return this.$store.state.management.user;
        }
    },
    watch: {
        '$route' (to, from) {
            document.title = to.meta.title || main_title;
        }
    },
    methods: {
        init() {
            if (window.localStorage.management_access_token) {
                window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.localStorage.management_access_token;
                window.axios.defaults.timeout = 30000;
            }
            document.title = main_title;
            window.axios.post('/management/api/auth/check', {})
                .then(response => {
                    if (response.data.returnCode === 0)
                    {
                        this.$store.state.management.user = response.data.data.user;
                        this.$store.state.management.menu = response.data.data.menu;
                        this.$store.state.management.unreadNotification = response.data.data.unreadNotifi;
                        if (this.$router.currentRoute.name === 'login') {
                            window.location.href = '/management/';
                        }
                    }else{
                        if (this.$router.currentRoute.name !== 'login') {
                            app.$router.push('/management/login');
                        }
                    }
                })
                .catch(error => {
                    if (this.$router.currentRoute.name !== 'login') {
                        app.$router.push('/management/login');
                    }
                });
        }
    },
    created() {
        this.init();
    }
});

app.$router.beforeEach((to, from, next) => {
    app.$store.state.is_route_loading = true;
    next();
});

app.$router.afterEach((to, from, next) => {
    // app.$store.state.is_route_loading = false;
});
