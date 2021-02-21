<template>
  <div class="time-add-and-sub">
    <h2>加减时间</h2>
    <v-row>
      <v-col cols="8">
        <date-time-form dateTime="paramDateTime" />
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
    <v-btn color="orange" class="mr-1" @click="calculation">计算时间</v-btn>
    <v-btn @click="resetCalculation">重置到当前时间</v-btn>
    <v-spacer />
    计算结果：{{ resultDateTime | dateTimeFormatter }}
  </div>
</template>

<script lang="ts">
import { Vue, Component } from "vue-property-decorator";
import DateTimeForm from "@/components/DateTimeForm.vue";
import dateTimeFormatter from "@/utils/dateTimeFilter";

@Component({
  components: {
    DateTimeForm
  },
  filters: {
    dateTimeFormatter
  }
})
export default class TimeAddAndSub extends Vue {
  paramDateTime = "";
  resultDateTime = new Date();
  calculationType = "byAdd";
  calculationRange: { [key: string]: number } = {
    anDays: 0,
    anHours: 0,
    anMinutes: 0,
    anSeconds: 0
  };
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
  resetCalculation() {
    this.paramDateTime = "";
    this.resultDateTime = new Date();
    for (const key in this.calculationRange) {
      this.calculationRange[key] = 0;
    }
  }
  currentDateTime() {
    const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
    return `${now.slice(0, 10)} ${now.slice(11, 19)}`;
  }
  created() {
    this.paramDateTime = this.currentDateTime();
  }
}
</script>
