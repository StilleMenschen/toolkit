<template>
  <div class="home">
    <h1>先导说明</h1>
    <p>所有工具基于Vue编写</p>
    <p>目前支持的工具如下</p>
    <v-simple-table dense>
      <template v-slot:default>
        <thead>
          <tr>
            <th>功能</th>
            <th>概述</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>字符串计算</td>
            <td>
              1. 计算字符串ASCII长度和非ASCII长度 <br />
              2. 支持记录字符串
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
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
  padding: 0.5rem 1rem;
}
</style>
