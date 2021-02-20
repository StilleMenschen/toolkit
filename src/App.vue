<template>
  <v-app>
    <v-navigation-drawer app width="180">
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">工具包</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item>
        <v-switch v-model="$vuetify.theme.dark" label="深色"></v-switch>
      </v-list-item>
      <v-divider></v-divider>

      <v-list dense nav>
        <v-list-item
          v-for="item in items"
          :key="item.title"
          :href="joinPath(item.link)"
          link
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>
              {{ item.title }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script lang="ts">
import Vue from "vue";
import Component from "vue-class-component";

interface NavItem {
  title: string;
  icon: string;
  link: string;
}

@Component
export default class App extends Vue {
  items: Array<NavItem> = [
    { title: "主页", icon: "mdi-home", link: "" },
    { title: "字符串", icon: "mdi-file-word-box", link: "words" },
    { title: "时间计算", icon: "mdi-file-word-box", link: "timer" },
    { title: "关于", icon: "mdi-help-box", link: "about" }
  ];

  joinPath(uri: string): string {
    return process.env.BASE_URL + uri;
  }

  created() {
    const hours = new Date().getHours();
    this.$vuetify.theme.dark = hours <= 6 || hours >= 18;
  }
}
</script>
