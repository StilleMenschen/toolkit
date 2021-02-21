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
          <tr v-for="item in overviews" :key="item.id">
            <td>{{ item.name }}</td>
            <td v-html="item.description"></td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

interface Overview {
  id: number;
  name: string;
  description: string;
}

@Component
export default class Home extends Vue {
  overviews: Array<Overview> = [
    {
      id: 1,
      name: "字符串字符计算",
      description: "1. 计算字符串ASCII长度和非ASCII长度 <br>2. 支持记录字符串"
    },
    { id: 2, name: "时间间隔计算", description: "1. 计算两个时间的间隔" },
    {
      id: 3,
      name: "加减时间",
      description: "1. 基于某个时间计算增加或减去后的时间"
    }
  ];
}
</script>

<style lang="less">
.home {
  padding: 0.5rem 1rem;
}
</style>
