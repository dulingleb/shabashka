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
import Header from '../common/components/Header.vue'
import Footer from '../common/components/Footer.vue'

import apiService from '../common/api.service'
import userService from '../common/user.service'

export default {
  props: ['csrfToken'],
  components: {
    'app-header': Header,
    'app-footer': Footer,
  },
  created() {
    apiService.init(this.csrfToken)
    this.$store.dispatch('GET_USER')
  },
}
</script> 

<style lang="scss" scoped>
  .app-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f2f8ff;

    > .container {
      margin-top: 30px;
      margin-bottom: 30px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      background-color: #fff;
    }
  }
</style>