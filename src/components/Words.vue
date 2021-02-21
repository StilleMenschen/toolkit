<template>
  <div class="words">
    <div>
      <v-textarea
        label="请输入待计算的字符串"
        v-model="word"
        clearable
        no-resize
        @click:clear="clearText"
        color="orange"
      ></v-textarea>
      <label class="orange--text" @click="warps" title="点击清除所有空格">
        ASCII字符长度 {{ word.length }}, 非ASCII字符长度
        {{ getStringRealLength }}</label
      >
      <input type="text" :value="word" ref="content" style="height: 0" />
    </div>
    <v-text-field
      class="mb-2"
      v-model="userKey"
      :counter="128"
      placeholder="存储参考Key - 只能英文字母数字,字母开头,最大128个字符"
      :rules="rules"
      clearable
      @click:clear="clearKey"
      required
      color="orange"
    ></v-text-field>
    <div>
      <v-btn
        @click="save"
        class="mr-1"
        :disabled="notWord"
        color="primary"
        elevation="2"
      >
        保存
      </v-btn>
      <v-btn
        @click="query"
        class="mr-1"
        :disabled="notKey"
        color="secondary"
        elevation="2"
      >
        查询
      </v-btn>
      <v-btn
        @click="copy"
        class="mr-1"
        :disabled="notWord"
        color="secondary"
        elevation="2"
      >
        复制
      </v-btn>
      <v-btn @click="clearItem" :disabled="notKey" color="red" elevation="2">
        删除所有
      </v-btn>
    </div>
    <div>
      <v-chip
        v-if="notice"
        close
        color="orange"
        @click:close="notice = false"
        class="ma-2"
      >
        点击下方列表的文字可填充至上方文本框中
      </v-chip>
      <v-chip class="ma-2 text--white" color="orange" v-if="hasMsg">
        {{ msg }}
      </v-chip>
      <v-chip class="ma-2 text--white" color="red" v-if="hasError">
        {{ msg }}
      </v-chip>
    </div>
    <v-radio-group v-model="pos" column>
      <v-row v-for="(v, i) in notes" :key="v">
        <v-col cols="7">
          <v-radio
            :key="i"
            :value="i"
            :label="v"
            @change="active(i)"
            color="success"
          />
        </v-col>
        <v-col cols="3">
          <v-btn fab small class="mr-2" @click="quickCopy(i)">
            <v-icon>mdi-content-copy</v-icon>
          </v-btn>
          <v-btn fab small @click="del(i)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-radio-group>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";
import axios, { AxiosResponse, Method } from "axios";

@Component
export default class Words extends Vue {
  private axiosInstance = axios.create({
    baseURL: "/",
    timeout: 3000
  });
  word = "";
  userKey = "";
  hasError = false;
  hasMsg = false;
  msg = "";
  notes: Array<string> = [];
  pos = -1;
  notice = true;
  rules: Array<Function> = [
    (value: string) => !!value || "请先填写Key值",
    (value: string) => {
      const pattern = /^[a-zA-Z][a-zA-Z0-9]{0,128}$/g;
      return pattern.test(value) || "只能英文字母数字,字母开头,最大128个字符";
    }
  ];

  $refs!: {
    content: HTMLInputElement;
  };

  get getStringRealLength() {
    const Str = this.word;
    let i: number, len: number, code: number;
    if (Str == null || Str == "") return 0;
    len = Str.length;
    for (i = 0; i < Str.length; i++) {
      code = Str.charCodeAt(i);
      if (code > 255) {
        len++;
      }
    }
    return len;
  }

  get notKey() {
    return !(
      this.userKey && /^[a-zA-Z][a-zA-Z0-9]{0,128}$/g.test(this.userKey)
    );
  }

  get notWord() {
    return this.word.length <= 0 || this.notKey;
  }

  warps() {
    if (/\s/g.test(this.word)) {
      this.word = this.word.replace(/\s/g, "");
    }
  }

  error(error: string) {
    this.hasError = true;
    this.msg = error;
    this.notes = [];
    this.pos = -1;
    setTimeout(() => {
      this.hasError = false;
    }, 3000);
  }

  success(response: AxiosResponse) {
    const obj = response.data;
    // console.log(response);
    if (obj.code < 0) {
      this.hasError = true;
      this.msg = obj.msg;
      this.notes = [];
      this.pos = -1;
      setTimeout(() => {
        this.hasError = false;
      }, 3000);
    } else {
      this.notes = obj.values;
    }
  }

  query() {
    this.api(
      "redis.php?o=all",
      "POST",
      {
        key: this.userKey
      },
      true
    );
  }

  active(index: number) {
    if (this.pos == index) {
      this.pos = -1;
    } else {
      this.pos = index;
      this.word = this.notes[index];
    }
  }

  copy() {
    try {
      this.$refs.content.select();
      document.execCommand("copy");
      this.msg = "复制成功";
      this.hasMsg = true;
    } catch (error) {
      this.msg = "复制失败";
      this.hasError = true;
    }
    setTimeout(() => {
      this.hasError = false;
      this.hasMsg = false;
    }, 3000);
  }

  quickCopy(index: number) {
    this.active(index);
    setTimeout(() => {
      this.copy();
    }, 600);
  }

  del(index: number) {
    this.pos = -1;
    this.api(
      "redis.php?o=rm",
      "POST",
      {
        key: this.userKey,
        value: this.notes[index]
      },
      true
    );
  }

  save() {
    let uri = "add";
    const values = {
      key: this.userKey,
      value: this.word,
      pos: ""
    };
    if (this.pos != -1) {
      uri = "set";
      values.pos = "0x" + this.pos.toString(16);
    }
    this.api("redis.php?o=" + uri, "POST", values, true);
  }

  clearItem() {
    if (this.notKey || !this.notes || this.notes.length <= 0) {
      return false;
    }
    if (confirm("确定要删除所有记录吗")) {
      this.api(
        "redis.php?o=rma",
        "POST",
        {
          key: this.userKey
        },
        false
      );
      this.notes = [];
      this.pos = -1;
    }
  }

  clearText() {
    this.word = "";
    this.pos = -1;
  }

  clearKey() {
    this.userKey = "";
    this.pos = -1;
    this.notes = [];
  }

  api(url: string, method: Method, values: object, isCall: boolean) {
    const p = this.axiosInstance.request({
      url: url,
      method: method,
      params: {
        ...values
      }
    });
    if (isCall) {
      p.then(this.success).catch(this.error);
    }
  }
}
</script>

<style lang="less">
.words {
  padding: 0.5rem 1rem;
}
</style>
