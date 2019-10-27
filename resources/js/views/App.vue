 <template>
  <div class="app-container">
    <app-header></app-header>

    <main class="container">
      <router-view></router-view>
    </main>

    <app-footer></app-footer>
  </div>
</template>

<script lang="ts">
import Header from "../common/components/Header.vue";
import Footer from "../common/components/Footer.vue";

export default {
  components: {
    "app-header": Header,
    "app-footer": Footer,
  }
};
</script> 

<style lang="scss" scoped>
  .app-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    > .container {
      flex-grow: 1;
    }
  }
</style>