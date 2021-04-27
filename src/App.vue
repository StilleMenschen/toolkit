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
          @click="redirect(item.routeName)"
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
  routeName: string;
}

@Component
export default class App extends Vue {
  items: Array<NavItem> = [
    { title: "主页", icon: "mdi-home", routeName: "Home" },
    { title: "字符串", icon: "mdi-file-word-box", routeName: "Words" },
    { title: "时间计算", icon: "mdi-timer-sand-full", routeName: "Timer" },
    { title: "关于", icon: "mdi-help-box", routeName: "About" }
  ];

  redirect(routeName: string) {
    if (this.$route.name == routeName) return false;
    else {
      this.$router.push({ name: routeName });
      return true;
    }
  }

  created() {
    const hours = new Date().getHours();
    this.$vuetify.theme.dark = hours <= 6 || hours >= 18;
  }
}
</script>
