<template>
  <v-row>
    <v-col cols="6">
      <v-menu
        v-model="menuOfDate"
        :close-on-content-click="false"
        :nudge-right="40"
        transition="scale-transition"
        offset-y
        min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="date"
            :label="`${labelPrefix}日期`"
            prepend-icon="mdi-calendar"
            readonly
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <v-date-picker
          v-model="date"
          color="orange"
          @input="menuOfDate = false"
        />
      </v-menu>
    </v-col>
    <v-col cols="6">
      <v-menu
        ref="menu"
        v-model="menuOfTime"
        :close-on-content-click="false"
        :nudge-right="40"
        :return-value.sync="time"
        transition="scale-transition"
        offset-y
        max-width="290px"
        min-width="290px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="time"
            :label="`${labelPrefix}时间`"
            prepend-icon="mdi-clock-time-four-outline"
            readonly
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <v-time-picker
          v-if="menuOfTime"
          v-model="time"
          full-width
          use-seconds
          format="24hr"
          color="orange"
          @click:second="$refs.menu.save(time)"
          @input="joinDateTime"
        ></v-time-picker>
      </v-menu>
    </v-col>
  </v-row>
</template>

<script>
import { Vue, Component, Prop, PropSync, Watch } from "vue-property-decorator";

@Component
export default class DateTimeForm extends Vue {
  @Prop({ type: String, default: "" }) labelPrefix;
  @PropSync("value", { type: String }) dateTime;
  menuOfDate = false;
  menuOfTime = false;
  date = "";
  time = "";

  joinDateTime() {
    return `${this.date} ${this.time}`;
  }

  @Watch("date") onDateChange() {
    this.dateTime = this.joinDateTime();
  }

  @Watch("time") onTimeChange() {
    this.dateTime = this.joinDateTime();
  }

  created() {
    const now = new Date(+new Date() + 8 * 60 * 60 * 1000).toISOString();
    this.date = now.slice(0, 10);
    this.time = now.slice(11, 19);
  }
}
</script>
