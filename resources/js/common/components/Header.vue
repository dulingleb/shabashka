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
              <b-form-input size="sm" class="mr-sm-2" placeholder="Поиск" v-model="searchValue" @input="search()"></b-form-input>
            </b-nav-form>

            <b-navbar-nav right>
              <router-link :to="{ name: 'newTask' }" size="sm" class="btn btn-warning add-task" type="submit">Создать задание</router-link>

              <b-nav-item v-if="!user" :to="{ name: 'login' }">Вход</b-nav-item>
            </b-navbar-nav>

            <b-nav-item-dropdown right v-if="user">
              <template v-slot:button-content>
              <span class="text-white">{{ userName }}</span>
              </template>
              <b-dropdown-item :to="{ name: 'profile' }">Профиль</b-dropdown-item>
              <b-dropdown-item :to="{ name: 'myTasks' }">Мои задания</b-dropdown-item>
              <b-dropdown-item @click="logout()">Выход </b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-collapse>
      </div>
    </b-navbar>
  </header>
</template>

<script lang="ts">

import _ from 'lodash'

import appRouter from '../../app.router'
import { capitalizeFirst } from '../utils'

export default {
  name: 'app-header',
  data() {
    return {
      searchValue: ''
    }
  },
  created() {
    this.searchValue = this.$router.currentRoute.query.search
    this.search = _.debounce(this.search, 1000)
  },
  computed: {
    user() {
      return this.$store.getters.user
    },
    userName() {
      return capitalizeFirst(this.user.name)
    }
  },
  methods: {
    logout() {
      this.$store.dispatch('LOGOUT')
      appRouter.push({ name: 'home' })
    },
    search() {
      this.$router.push({ query: { search: this.searchValue.trim() } })
    }
  },
  components: {}
}
</script> 

<style lang="scss" scoped>
  header {
    box-shadow: 0px 0px 12px -5px black;
    .add-task {
      height: 32px;
      line-height: 1;
      margin-top: 5px;
    }
  }
</style>