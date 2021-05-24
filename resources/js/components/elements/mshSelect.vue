<template>
    <div class="msh-select-holder" @click="isSelecting = true">
        <div class="available-items" v-show="isSelecting">
            <span v-for="item in filteredItems"  @click="toggleItem(item)" :class="{'selected' : item.selected}" :key="item.label">{{item.label}}</span>
            <span disabled="disabled" class="disabled" v-if="Object.keys(filteredItems).length === 0">داده ای وجود ندارد</span>
        </div>
        <ul @click="setInputFocuse()" v-click-outside="hideSelectOption">
            <li v-for="item in options.filter(item => item.selected)" @click="toggleItem(item)" :key="item.label">
                <span>{{item.label}}</span>
            </li>
            <li class="input-holder">
                <input class="msh-input" v-model="text" :size="inputSize" placeholder="انتخاب ..." @keydown.enter="addNewTag()">
            </li>
        </ul>
    </div>
</template>

<script>
    import directives from "../../includes/directives";

    export default {
        name: "mshSelect",
        directives: directives,
        props: {
            options: {},
            handler: null
        },
        data() {
            return {
                items: [],
                currentItem: {},
                text: '',
                isSelecting: false
            }
        },
        watch: {
            selectedValues(selectedValues) {
                this.$emit(this.handler, selectedValues);
            },
            options(options) {
                // update
                this.items = options;
            }
        },
        methods: {
            toggleItem(item)
            {
                item.selected = !item.selected;
                this.text = '';
            },
            hideSelectOption(event)
            {
                this.isSelecting = false;
            },
            setInputFocuse() {
                this.$el.querySelector('ul > li > input').focus();
            },
            addNewTag() {
                this.items.push({
                    'selected' : true,
                    'label': this.text,
                    'value' : 0,
                })
                this.text = '';
            }
        },
        // mounted() {
        //     this.items = this.options;
        // },
        computed: {
            filteredItems() {
                let pattern = new RegExp(this.text);
                return this.items.filter( item => {
                    return pattern.test(item.label)
                })
            },
            inputSize() {
                return this.text.length > 5 ? this.text.length : 5;
            },
            selectedValues() {
                return this.items.filter( item => {
                    return item.selected;
                })
            }
        },
        filters: {

        }
    }
</script>

<style lang="sass">
    .msh-select-holder
        position: relative
        display: block
        width: 100%
        border: 1px solid #f6f6f8
        padding: 5px
        border-radius: 0.6rem
        z-index: 1
        background-color: #f6f6f8
        .available-items
            position: absolute
            right: 0
            left: 0
            top: 45px
            max-height: 150px
            height: auto
            overflow: auto
            background: #ffffff
            border: 1px solid #cccccc
            border-radius: 5px
            span
                display: block
                padding: 5px 10px
                cursor: pointer
                &.selected
                    background: #dcdcdc
                &:hover
                    background-color: #fcfcfc
        ul
            padding: 0
            margin: 0
            cursor: text
            li
                display: inline-block
                span
                    display: block
                    border: 1px solid #ccc
                    background-color: #fff
                    padding: 0px 5px
                    border-radius: 5px
                    color: #666
                    margin: 2px
                    font-size: 13px
                    cursor: pointer
                &.input-holder
                    position: relative
                    display: inline-block
            input
                display: inline-block
                margin-top: 5px
                border: 0 !important
                box-shadow: none !important
                outline: none !important
                width: auto
                background-color: #f6f6f8
</style>
