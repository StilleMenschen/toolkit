<template>
  <div class="time-interval">
    <h2>时间间隔</h2>
    <v-spacer />
    <v-row>
      <v-col cols="8">
        <date-time-form label-prefix="开始" dateTime="start" />
      </v-col>
    </v-row>
    <v-spacer />
    <v-row>
      <v-col cols="8">
        <date-time-form label-prefix="结束" dateTime="end" />
      </v-col> </v-row
    ><v-spacer /> 统计类型：<label v-for="v in calculateRange" :key="v.id">
      <input type="checkbox" @change="calculate" v-model="v.checked" />
      {{ v.description }} </label
    ><v-spacer />
    <v-btn color="orange" class="mr-1" @click="calculate">计算间隔</v-btn>
    <v-btn @click="resetCalculate">重置到当前时间</v-btn>
    <v-spacer />
    时间间隔为
    <span v-for="v in calculateRange" :key="v.id * 10" v-show="v.checked">
      {{ v.value }}{{ v.description }}
    </span>
  </div>
</template>

<script lang="ts">
import { Vue, Component } from "vue-property-decorator";
import DateTimeForm from "@/components/DateTimeForm.vue";
import dateTimeFormatter from "@/utils/dateTimeFilter";

interface TempRange {
  cache: number;
  divisor: number;
  key: string;
}

interface Range {
  id: number;
  description: string;
  millisecond: number;
  value: number | string;
  checked: boolean;
}

@Component({
  components: {
    DateTimeForm
  },
  filters: {
    dateTimeFormatter
  }
})
export default class TimeInterval extends Vue {
  start = "";
  end = "";

  calculateRange: { [key: string]: Range } = {
    days: {
      id: 1,
      description: "天",
      millisecond: 86400000,
      value: 0,
      checked: false
    },
    hours: {
      id: 2,
      description: "小时",
      millisecond: 3600000,
      value: 0,
      checked: true
    },
    minutes: {
      id: 3,
      description: "分钟",
      millisecond: 60000,
      value: 0,
      checked: false
    },
    seconds: {
      id: 4,
      description: "秒",
      millisecond: 1000,
      value: 0,
      checked: false
    }
  };
  calculateTemp: TempRange = {
    cache: 0,
    divisor: 0,
    key: ""
  };

  currentDateTime(): string {
    const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
    return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
  }

  calculateOfValue(
    base: number,
    divisor: number,
    key: string,
    fixed?: boolean
  ): number {
    if (fixed) {
      this.calculateRange[key].value = Number(base / divisor).toFixed(2);
      return 0;
    } else {
      if (Math.abs(base) < divisor) {
        this.calculateRange[key].value = 0;
        return base;
      }
      this.calculateTemp = { cache: base, divisor: divisor, key: key };
      this.calculateRange[key].value = parseInt(`${base / divisor}`);
      return base % divisor;
    }
  }

  calculate() {
    const tempStart = new Date(this.start).getTime();
    const tempEnd = new Date(this.end).getTime();
    let v = tempEnd - tempStart;
    if (Math.abs(v) < 1000) {
      return false;
    }
    for (const key in this.calculateRange) {
      if (Object.hasOwnProperty.call(this.calculateRange, key)) {
        const element = this.calculateRange[key];
        if (element.checked) {
          v = this.calculateOfValue(v, element.millisecond, key);
        }
      }
    }
    // 重算小数点
    if (Math.abs(v) > 0) {
      const { cache, divisor, key } = this.calculateTemp;
      this.calculateOfValue(cache, divisor, key, true);
    }
  }

  resetCalculate() {
    this.start = new Date().toISOString().slice(0, 10);
    this.end = new Date().toISOString().slice(0, 10);
    for (const key in this.calculateRange) {
      this.calculateRange[key].value = 0;
    }
  }

  created() {
    this.start = this.currentDateTime();
    this.end = this.currentDateTime();
  }
}
</script>
