<template>
  <div class="loading">
    <b-spinner variant="info" label="Spinning"></b-spinner>
    <b-spinner variant="info" type="grow" label="Spinning"></b-spinner>
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
  }

</style>