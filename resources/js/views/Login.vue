 <template>
  <div class="">
    <div class="page-title">
      <h2 class="title">Авторизация</h2>
    </div>

    <div class="page-content">
      <b-form @submit.prevent="onSubmit" @reset.prevent="onReset">
        <b-form-group  name="email" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="email">Email</label>
            <div class="col-md-7">
              <b-form-input class="" id="email" v-model="form.email" :state="validateEmail" type="email" required placeholder="Введите email" autofocus :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validateEmail">Некорректный email</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <b-form-group name="password" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="password">Пароль:</label>
            <div class="col-md-7">
              <b-form-input id="password" v-model="form.password" :state="validatePassword" type="password" required placeholder="Введите пароль" :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validatePassword">Некорректный пароль. Должен быть длинее 3-х символов</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <div class="row">
          <app-messages class="offset-md-3 col-md-7" :messages="errReponseMessages"></app-messages>
        </div>

        <b-form-group>
          <div class="row">
            <div class="offset-md-3 col-md-7">
              <b-button type="submit" variant="outline-info" :disabled="loading">Войти</b-button>
              <router-link class="btn btn-link text-info" :to="{ name: 'register' }">Регистрация</router-link>
              <a class="btn btn-link text-info" href="">Забыли пароль?</a>
            </div>
          </div>
        </b-form-group>

      </b-form>
    </div>

  </div>
</template>

<script lang="ts">
import userService from '../common/user.service'
import appRouter from '../app.router'
import { getMessage } from '../common/utils'

export default {
  name: 'app-login',
  data() {
    return {
      loading: false,
      form: {
        email: '',
        password: ''
      },
      formDirty: false,
      errReponseMessages: [],
    }
  },
  computed: {
    validateEmail() {
      return !this.formDirty || !!this.form.email.match(/\S+@\S+\.\S+/)
    },
    validatePassword() {
      return !this.formDirty || this.form.password.length > 5
    }
  },
  watch: {
    form: {
      handler() {
        this.formDirty = true
      },
      deep: true
    }
  },
  methods: {
    async onSubmit(evt) {
      this.loading = true
      const result = await userService.auth(this.form.email, this.form.password)
      if (!result.error) {
        await this.$store.dispatch('GET_USER')
        appRouter.push({ name: 'home' })
        return
      }
      this.errReponseMessages.push(getMessage(true, '', result.error))
      this.loading = false
    },
    onReset(evt) {
      this.form.email = ''
      this.form.password = ''
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>