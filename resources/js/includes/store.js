import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        management: {
            user: null,
            menu: null,
            access_token: null,
            unreadNotification: 0,
        },
        is_route_loading: false,
        breadcrumb: [],
        aside_is_open: false
    }
});
