<template>
  <div class="timer">
    <time-interval />
    <v-divider />
    <time-add-and-sub />
  </div>
</template>

<script lang="ts">
import { Vue, Component } from "vue-property-decorator";
import TimeInterval from "@/components/TimeInterval.vue";
import TimeAddAndSub from "@/components/TimeAddAndSub.vue";

@Component({
  components: {
    TimeAddAndSub,
    TimeInterval
  }
})
export default class Timer extends Vue {}
</script>

<style lang="less">
.timer {
  padding: 0.5rem 1rem;
}
</style>
