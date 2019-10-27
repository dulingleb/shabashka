<template>
  <div class="row">
        <div class="col-md-4 col-lg-3">
          <app-aside></app-aside>
        </div>
        <div class="col-md-8 col-lg-9">
           CONTENT
        </div>
      </div>
</template>

<script lang="ts">
import Aside from "../common/components/Aside.vue";

export default {
  components: {
    "app-aside": Aside,
  }
};
</script> 

<style lang="scss" scoped>

</style>