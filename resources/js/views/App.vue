 <template>
  <div>
    <app-header></app-header>
    <h1>Vue Router Demo App</h1>

    <p>
      <router-link :to="{ name: 'home' }">Home</router-link> |
      <router-link :to="{ name: 'hello' }">Hello World</router-link>
    </p>

    <div class="container">
      <router-view></router-view>
    </div>
  </div>
</template>

<script lang="ts">
  import Header from '../common/components/Header.vue';

  export default {
    components: {
      'app-header': Header
    }
  }
</script> 

<style lang="scss" scoped>

</style>