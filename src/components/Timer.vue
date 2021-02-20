<template>
  <div class="timer">
    <h2>时间间隔</h2>
    <v-spacer />
    <v-row>
      <v-col cols="8">
        <date-time-form label-prefix="开始" @calc="setStart" />
      </v-col>
    </v-row>
    <v-spacer />
    <v-row>
      <v-col cols="8">
        <date-time-form label-prefix="结束" @calc="setEnd" />
      </v-col> </v-row
    ><v-spacer />
    <label v-for="v in calculateRange" :key="v.id">
      <input type="checkbox" @change="calculate" v-model="v.checked" />
      {{ v.description }} </label
    ><v-spacer />
    <v-btn @click="resetCalculate" class="mr-1">重置到当前时间</v-btn>
    <v-btn @click="calculate">计算间隔</v-btn>
    <v-spacer />
    时间间隔为
    <span v-for="v in calculateRange" :key="v.id * 10" v-show="v.checked">
      {{ v.value }}{{ v.description }}
    </span>
    <v-spacer />
    <h2>加减时间</h2>
    <v-row>
      <v-col cols="8">
        <date-time-form @calc="setParamDateTime" />
      </v-col>
    </v-row>
    <v-spacer />
    计算方式：
    <label
      ><input
        type="radio"
        name="calculationType"
        value="byAdd"
        v-model="calculationType"
      />添加</label
    >
    <label
      ><input
        type="radio"
        name="calculationType"
        value="byMinus"
        v-model="calculationType"
      />减去</label
    >
    <v-spacer />
    <input
      type="number"
      class="miniInput"
      min="0"
      v-model="calculationRange.anDays"
    />天
    <input
      type="number"
      class="miniInput"
      min="0"
      v-model="calculationRange.anHours"
    />时
    <input
      type="number"
      class="miniInput"
      min="0"
      v-model="calculationRange.anMinutes"
    />分
    <input
      type="number"
      class="miniInput"
      min="0"
      v-model="calculationRange.anSeconds"
    />秒
    <v-spacer />
    <v-btn @click="resetCalculation" class="mr-1">重置到当前时间</v-btn>
    <v-btn @click="calculation">计算时间</v-btn>
    <v-spacer />
    计算结果：{{ resultDateTime | dateTimeFormatter }}
  </div>
</template>

<script lang="ts">
import { Vue, Component } from "vue-property-decorator";
import DateTimeForm from "@/components/DateTimeForm.vue";

interface TempRange {
  cache: number;
  divisor: number;
  key: string;
}

interface Range {
  id: number;
  description: string;
  millisecond: number;
  value: number;
  checked: boolean;
}

@Component({
  components: {
    DateTimeForm
  },
  filters: {
    dateTimeFormatter(value: Date) {
      if (!value || Object.prototype.toString.call(value) !== "[object Date]")
        return "1970-01-01 00:00:00";
      const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
      return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
    }
  }
})
export default class Timer extends Vue {
  start = "";
  end = "";
  paramDateTime = "";
  resultDateTime = new Date();
  calculationType = "byAdd";
  calculationRange: { [key: string]: number } = {
    anDays: 0,
    anHours: 0,
    anMinutes: 0,
    anSeconds: 0
  };
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

  currentDateTime() {
    const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
    return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
  }

  setParamDateTime(value: string) {
    this.paramDateTime = value;
  }

  setStart(value: string) {
    this.start = value;
  }

  setEnd(value: string) {
    this.end = value;
  }

  calculateOfValue(
    base: number,
    divisor: number,
    key: string,
    fixed?: boolean
  ) {
    if (fixed) {
      const result = base / divisor;
      this.calculateRange[key].value = parseFloat(result.toFixed(2));
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
    console.log("calculate ->", this.start, this.end);
    const tempStart = new Date(this.start).getTime();
    const tempEnd = new Date(this.end).getTime();
    let v: any = tempEnd - tempStart;
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

  calculation() {
    const { anDays, anHours, anMinutes, anSeconds } = this.calculationRange;
    const type = this.calculationType == "byAdd" ? 1 : -1;
    const result =
      type *
      (anDays * 86400000 +
        anHours * 3600000 +
        anMinutes * 60000 +
        anSeconds * 1000);
    const param = new Date(this.paramDateTime);
    this.resultDateTime = new Date(param.getTime() + result);
  }

  resetCalculate() {
    this.start = new Date().toISOString().slice(0, 10);
    this.end = new Date().toISOString().slice(0, 10);
    for (const key in this.calculateRange) {
      this.calculateRange[key].value = 0;
    }
  }

  resetCalculation() {
    this.paramDateTime = "";
    this.resultDateTime = new Date();
    for (const key in this.calculationRange) {
      this.calculationRange[key] = 0;
    }
  }

  created() {
    this.start = this.currentDateTime();
    this.end = this.currentDateTime();
    this.paramDateTime = this.currentDateTime();
  }
}
</script>

<style lang="less">
.timer {
  padding: 0.5rem 1rem;
}
</style>
