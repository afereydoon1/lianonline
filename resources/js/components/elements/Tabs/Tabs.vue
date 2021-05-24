<template>
    <div class="msh-tabs">
        <ul class="msh-tabs-buttons">
            <li v-for="tab in tabs" :class="{'is_active' : tab.is_active}">
                <a @click="selectTab(tab)">{{tab.title}}</a>
            </li>
        </ul>
        <div class="msh-tabs-contents-holder">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Tabs",
        data() {
            return {
                tabs: []
            }
        },
        methods: {
            selectTab(newTab)
            {
                this.tabs.forEach(tab => {
                    tab.is_active = tab.name === newTab.name;
                    tab.reset();
                });
            }
        },
        created() {
            this.tabs = this.$children;
        }
    }
</script>

<style scoped>

</style>
