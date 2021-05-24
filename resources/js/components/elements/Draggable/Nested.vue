<template>
    <draggable class="dragArea" tag="ul" :list="records" :group="{ name: 'g1' }">
        <li v-for="el in records" :key="el.title">
            <p>{{ el.title }}</p>
            <nested-draggable :records="el.records" class="child"  />
        </li>
    </draggable>
</template>
<script>
    import draggable from "vuedraggable";
    export default {
        props: {
            records: {
                required: true,
                type: Array
            }
        },
        components: {
            draggable
        },
        name: "nested-draggable"
    };
</script>
<style lang="sass">
    .dragArea
        min-height: 30px
        position: relative
        background-color: #dcdcdc
        border:1px solid #ececec
        /*outline: 1px dashed*/
        &.child
            margin-right: 15px
            &> li > p
                border: 1px solid #ccc
        /*&::after*/
        /*    content: ''*/
        /*    display: block*/
        /*    position: absolute*/
        /*    width: 2px*/
        /*    top: 0px*/
        /*    height: 55px*/
        /*    background-color: #dcdcdc*/
</style>
