<template>
  <div class="home">
    <h1>先导说明</h1>
    <p>所有工具基于Vue编写</p>
    <p>目前支持的工具如下</p>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

@Component
export default class Home extends Vue {}
</script>

<style lang="less">
.home {
  text-align: center;
}
</style>
