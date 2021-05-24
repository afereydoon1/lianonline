<template>
    <div class="content-viewer" :class="{'is-login-page': !userInfo}">
        <div class="header-holder" v-if="userInfo">
            <div class="app-menu-toggle position-absolute d-lg-none">
                <div class="toggle-btn">
                    <button id="app-toggle-mobile-menu" @click="toggleMenu" class="toggle-quru toggle-daha" :class="{'is-active': asideStatus}">
                        <span>toggle menu</span>
                    </button>
                </div>
            </div>

            <h2 class="page-title mb-2 font-20 mt-1 bold-500 item right"><span class="fas fa-dot-circle font-14 ml-2 text-secondary"></span>پنل کاربری</h2>

            <div class="float-left left-btns">
                <router-link :to="{name: 'posts.create'}" class="header-btn btn btn-light position-relative p-2 m-1 rounded-circle">
                    <i class="fas fa-plus-square"></i>
                    <span class="tooltip">نوشته جدید</span>
                </router-link>
                <router-link :to="{name: 'product.create'}" class="header-btn btn btn-light position-relative p-2 m-1 rounded-circle">
                    <i class="fas fa-cart-plus"></i>
                    <span class="tooltip">محصول جدید</span>
                </router-link>
                <router-link :to="{name: 'notifications.list'}" class="header-btn btn btn-light position-relative p-2 m-1 rounded-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="اطلاعیه ها">
                    <i class="fas fa-bell"></i>
                    <span class="tooltip">اطلاعیه ها</span>
                    <span v-if="unreadNotifi > 0" class="al ellipsis">{{unreadNotifi}}</span>
                </router-link>
            </div>
        </div>
        <div :class="{'pt-3 pb-3': userInfo}">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ContentViewer",
        methods: {
            toggleMenu()
            {
                this.$store.state.aside_is_open = !this.$store.state.aside_is_open;
            }
        },
        computed: {
            asideStatus() {
                return this.$store.state.aside_is_open;
            },
            asideViewable() {
                return this.$store.state.management.user;
            },
            userInfo() {
                return this.$store.state.management.user;
            },
            unreadNotifi() {
                return this.$store.state.management.unreadNotification;
            }
        },
    }
</script>
<style lang="sass">
.content-viewer
    display: block
    padding: 15px
    .header-holder
        border-bottom: 1px solid #f8f9fa
        display: inline-block
        vertical-align: middle
        width: 100%
        padding-bottom: 15px
        .item
            display: inline-block
            &.left
                float: left
            &.right
                float: right
        .left-btns
            a
                position: relative
                .tooltip
                    opacity: 0
                    top: 45px
                    left: 50%
                    transform: translateX(-50%)
                    background: #5acaff
                    color: #ffffff
                    padding: 5px 8px 3px 8px
                    border-radius: 5px
                    white-space: nowrap
                    transition: opacity 0.5s
                    &::after
                        display: block
                        position: absolute
                        top: -10px
                        left: 50%
                        transform: translateX(-50%)
                        content: ''
                        width: 0
                        height: 0
                        border-left: 5px solid transparent
                        border-right: 5px solid transparent
                        border-bottom: 10px solid #5acaff
                &:hover
                    .tooltip
                        opacity: 1
            i
                display: block
                width: 20px
                height: 20px
                color: #4a4a4a
                font-size: 17px
                line-height: 20px
        .ellipsis
            display: block
            position: absolute
            right: 4px
            bottom: -8px
            width: 16px
            height: 16px
            color: #fff
            background-color: #fa600b
            font-size: 10px
            font-weight: 300
            line-height: 16px
            border-radius: 50rem
@media screen and (max-width: 992px)
    .page-title
        margin-right: 25px
</style>
<style scoped>
.aside .avatar-holder {
    display: block;
    width: 218px;
    margin: 0px auto;
    padding: 0;
    text-align: center;
}

.aside .avatar-holder img {
    display: block;
    width: 96px;
    height: 96px;
    margin: 0px auto;
}

.aside .lian-logo-holder {
    display: block;
    width: 218px;
    margin: 10px auto;
    padding: 5px;
}

.content-viewer {
    display: block;
    padding: 15px;
}

.content-viewer .header-holder {
    border-bottom: 1px solid #f8f9fa;
    display: inline-block;
    vertical-align: middle;
    width: 100%;
    padding-bottom: 15px;
}
</style>
