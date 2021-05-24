<template>
    <div class="">
        <div class="msh-tabs-content" v-show="is_active">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Tab",
        props: {
            name: {required: true},
            title: {required: true},
            default_active: {default: false}
        },
        data() {
            return {
                is_active: false
            }
        },
        methods: {
            reset() {
                this.$children[0].reset();
            }
        },
        mounted() {
            this.is_active = this.default_active;
        }
    }
</script>

<style scoped>

</style>
