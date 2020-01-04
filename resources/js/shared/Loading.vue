<template>
  <div class="loading">
    <!-- <b-spinner variant="info" label="Spinning"></b-spinner> -->
    <b-spinner class="spinner" variant="info" type="grow" label="Spinning"></b-spinner>
  </div>
</template>

<script lang="ts">

export default {
  name: 'app-loading',
  data() {
    return {
    }
  }
}
</script> 

<style lang="scss" scoped>
  .loading {
    padding: 15px;
    text-align: center;
    .spinner {
      width: 3rem;
      height: 3rem;
    }
  }

</style>