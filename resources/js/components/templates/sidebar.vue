<template>
    <aside class="aside" v-if="userInfo">
        <div class="lian-logo-holder">
            <img src="/images/management/logo.png" alt="lian-logo" style="cursor:pointer;" @click="forseRefresh()">
        </div>
        <div class="avatar-holder">
            <a>
                <img src="/images/management/avatar.png" alt="lian-logo">
                <span class="ellipsis font-14 bold-600 text-dark mt-2">{{userInfo.name}}</span>
            </a>
        </div>
        <ul class="aside-menu">
            <li>
                <router-link :to="{name: 'dashboard'}" active-class="active" exact>
                    <span class="link-icon"><i class="fas fa-desktop"></i></span>
                    <span class="link-text">داشبورد</span>
                </router-link>
            </li>
            <li>
                <router-link :to="{name: 'notifications.list'}" active-class="active" exact>
                    <span class="link-icon"><i class="fas fa-bell"></i></span>
                    <span class="link-text">اطلاعیه ها</span>
                </router-link>
            </li>
            <li v-for="item in menu" :class="{'open' : item.open}" :key="item.title">
                <router-link :to="{name: item.route}" active-class="active" v-if="item.route">
                    <span class="link-icon" v-html="item.icon"></span>
                    <span class="link-text">{{ item.title}}</span>
                </router-link>
                <a v-if="!item.route" class="menu-parent-node" @click="toggleSubMenu(item)">
                    <span class="link-icon" v-html="item.icon"></span>
                    <span class="link-text">{{ item.title}}</span>
                    <span class="link-arrow"></span>
                </a>
                <ul v-if="item.childs" class="children mb-2 mx-0 p-0">
                    <li v-for="child in item.childs">
                        <router-link :to="{name: child.route}" active-class="active" v-if="child.route">
                            <span class="link-icon"><i class="fas fa-dot-circle"></i></span>
                            <span class="link-text">{{ child.title}}</span>
                        </router-link>

                    </li>
                </ul>
            </li>
            <li>
                <router-link :to="{name: 'logout'}" active-class="active" exact>
                    <span class="link-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span class="link-text">خروج</span>
                </router-link>
            </li>
        </ul>
    </aside>

</template>

<script>
    import axios from 'axios';
    export default {
        name: "sidebar",
        data() {
            return {
                // menus: {}
            };
        },
        computed: {
            userInfo() {
                return this.$store.state.management.user;
            },
            menu() {
                return this.$store.state.management.menu;
            }
        },
        watch: {
            $route (to, from){
                this.show = false;
            }
        },
        mounted() {
            // this.getMenuItems();
        },
        methods: {
            getMenuItems() {
                axios.post('/panel/api/config/getMenuInfo', {}, {timeout: 1500})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.menus = response.data.data;
                        }
                        else
                        {

                        }

                    })
                    .catch(error => {

                    });
            },
            doLogout() {
                axios.post('/logout', {}, {timeout: 1500})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            window.location.href = '/';
                        }
                        else
                        {

                        }

                    })
                    .catch(error => {

                    });
            },
            toggleSubMenu(item) {
                item.open = !item.open;
                item.height = (item.childs.lengh * 41) + 'px';
            },
            forseRefresh() {
                if(this.$router.name == 'dashboard') {
                    this.$router.go(0);
                }else{
                    window.location.href = '/';
                }

            }
        },
    }
</script>


<style lang="sass">
.aside
    .lian-logo-holder
        display: block
        width: 218px
        margin: 10px auto
        padding: 5px
        img
            display: block
            width: 128px
            margin: auto
    .avatar-holder
        display: block
        width: 218px
        margin: 0 auto
        padding: 0
        text-align: center
        img
            display: block
            width: 96px
            height: 96px
            margin: 0 auto
    .aside-menu
        display: block
        margin: 15px
        padding-bottom: 50px
        li
            width: 100%
            position: relative
            background-color: transparent
            transition: background-color 0.2s
            ul
                display: block
                opacity: 0
                visibility: hidden
                height: 0px
                transition: opacity 0.1s, visibility 0.1s
                overflow: hidden
            a
                display: table
                width: 100%
                color: #000
                font-size: 14px
                font-weight: 300
                line-height: 25px
                padding: 8px
                cursor: pointer
                .link-icon
                    display: table-cell
                    width: 25px
                    vertical-align: middle
                    text-align: center
                .link-text
                    display: table-cell
                    vertical-align: middle
                    padding-left: 10px
                    padding-right: 5px
                .link-arrow
                    display: table-cell
                    width: 25px
                    vertical-align: middle
                    position: relative
                    &::before
                        content: "\f107"
                        position: absolute
                        left: 8px
                        top: 2px
                        color: #aaa
                        font-weight: 900
                        line-height: 25px
                        font-family: "Font Awesome 5 Free"
            &.open
                background-color: #eee
                ul
                    opacity: 1
                    visibility: visible
                    height: auto
                    li
                        a
                            line-height: 15px
                            font-size: 12px
                            padding-right: 10px
                            color: #4e4e4e
                            .link-icon
                                color: #aaa
                            &:hover
                                color: #222
                .link-arrow
                    &::before
                        content: "\f106"
                        position: absolute
                        left: 8px
                        top: 2px
                        color: #aaa
                        font-weight: 900
                        line-height: 25px
                        font-family: "Font Awesome 5 Free"
            &.logout-item
                a
                    color: #666
                    &:hover
                        color: #000
</style>
