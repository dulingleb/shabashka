<template>
  <div class="row">
        <div class="col-md-4 col-lg-3">
          <app-aside></app-aside>
        </div>
        <div class="col-md-8 col-lg-9">
           <div class="users">
    <div class="loading" v-if="loading">
      Loading...
    </div>

    <div v-if="error" class="error">
      {{ error }}
    </div>

    <ul v-if="users">
      <li v-for="{ name, email } in users">
        <strong>Name:</strong> {{ name }},
        <strong>Email:</strong> {{ email }}
      </li>
    </ul>
  </div>
        </div>
      </div>
</template>

<script lang="ts">
import Aside from "../common/components/Aside.vue";
import userService from "../common/user.service";
import axios from 'axios';

export default {
  data() {
    return {
      loading: false,
      users: null,
      error: null,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.error = this.users = null;
      this.loading = true;
      const users = await userService.getTestUsers();
      this.users = users;
      this.loading = false;
    }
  },
  components: {
    "app-aside": Aside,
  }
};
</script> 

<style lang="scss" scoped>

</style>