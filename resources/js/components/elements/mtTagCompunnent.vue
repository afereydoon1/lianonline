<template>
  <span class="mttag-main">
    <ul class="mttag-ul">
      <li
        v-if="value"
        v-for="tag in value"
        :key="tag"
        class="mttag-li"
        :title="tag"
        data-select2-id="220"
      >
        <span class="mttag-li-remove" @click="remove(tag)">×</span>
        {{tag}}
      </li>

      <li class="mttag-li-search">
        <input v-model="fills" class="mttag-input-search" type="text" @keydown.enter="atEnter()" />
      </li>
    </ul>
  </span>
</template>
<script>
export default {
  name: "mtTagCompunnent",
  props: {
    value: {
      type: Array,
    },
  },
  data() {
    return {
      fills: "",
    };
  },
  computed: {},
  methods: {
    atEnter() {
      this.value.push(this.fills);
      this.fills = "";
      this.$emit('input',this.value);
    },
    remove(tag) {
      this.value.splice(this.value.indexOf(tag), 1);
      this.$emit('input',this.value);
    },
  },
};
</script>
<style scoped>
.mttag-ul {
  display: flex;
  flex-wrap: wrap;
}
.mttag-li {
  margin-left: 5px;
  margin-bottom: 5px;
  background-color: #e4e4e4;
  border: 1px solid #aaa;
  padding: 5px;
  direction: ltr;
  border-radius: 4px;
}
.mttag-li-search {
  flex: 1;
}
.mttag-input-search {
  border: 0px !important;
  background: transparent !important;
  padding: 5px;
  margin-right: 5px;
  margin-top: 5px;
}
.mttag-input-search:focus,
.mttag-input-search:active{
  border: 0px solid transparent !important;
  padding: 5px;
}
.mttag-li-remove {
  cursor: pointer;
  padding-right: 2px;
  padding-left: 2px;
}
</style>
