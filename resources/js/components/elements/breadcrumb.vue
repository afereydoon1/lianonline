<template>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{name: 'dashboard'}">داشبورد</router-link>
            </li>
            <li class="breadcrumb-item" v-for="(item, index) in breadcrumb" :class="{'active' : totalItems === index + 1}">
                <span v-if="totalItems === index + 1">{{item.title}}</span>
                <router-link v-if="totalItems !== index + 1" :to="{name: item.name}">{{item.title}}</router-link>
            </li>
        </ol>
    </div>
</template>

<script>
    export default {
        name: "breadcrumb",
        computed: {
            breadcrumb() {
                return this.$store.state.breadcrumb;
            },
            totalItems() {
                return this.$store.state.breadcrumb.length;
            }
        }
    }
</script>

<style scoped>

</style>
