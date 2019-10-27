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
    fetchData() {
      this.error = this.users = null;
      this.loading = true;
      axios
        .get('/api/users')
        .then(response => {
          console.log(response);
        });
    }
  },
  components: {
    "app-aside": Aside,
  }
};
</script> 

<style lang="scss" scoped>

</style>