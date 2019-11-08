 <template>
  <header>
    <b-navbar toggleable="lg" type="dark" variant="info">
      <div class="container">
        <b-navbar-brand :to="{ name: 'home' }">Shabashki</b-navbar-brand>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
          <b-navbar-nav>
            <b-nav-item :to="{ name: 'hello' }">Hello World</b-nav-item>
          </b-navbar-nav>

          <!-- Right aligned nav items -->
          <b-navbar-nav class="ml-auto">
            <b-nav-form>
              <b-form-input size="sm" class="mr-sm-2" placeholder="Search"></b-form-input>
              <b-button size="sm" class="my-2 my-sm-0" type="submit">Search</b-button>
            </b-nav-form>

            <b-navbar-nav right>
              <b-nav-item v-if="!user" :to="{ name: 'login' }">Вход</b-nav-item>
            </b-navbar-nav>

            <b-nav-item-dropdown right v-if="user">
              <!-- Using 'button-content' slot -->
              <template v-slot:button-content>
                <em>User</em>
              </template>
              <b-dropdown-item :to="{ name: 'profile' }">Профиль</b-dropdown-item>
              <b-dropdown-item @click="logout()">Выход </b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-collapse>
      </div>
    </b-navbar>
  </header>
</template>

<script lang="ts">

  import appRouter from '../../app.router'
  
  export default {
    name: "app-header",
    data() {
      return {
      }
    },
    created() {
      
    },
    computed: {
      user() {
        return this.$store.getters.user
      }
    },
    methods: {
      logout() {
        this.$store.dispatch('LOGOUT')
        appRouter.push({ name: 'home' })
      }
    },
    components: {}
  };
</script> 

<style lang="scss" scoped>
  header {
    box-shadow: 0px 0px 12px -5px black;
  }
</style>