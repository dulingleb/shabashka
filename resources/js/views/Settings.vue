<template>
  <div class="">
    <div class="page-title">
      <h2 class="title">Настройки</h2>
    </div>

    <div class="page-content">

      <b-form @submit="onSubmit" @reset="onReset">
        <div class="row" v-if="user">
          <div class="col-md-4 col-lg-2">
            <app-avatar :title="userName" :is-edit="true" :image="user.logo" @change-file="changeAvatar" :font-size="'112px'"></app-avatar>
          </div>
          <div class="col-md-8 col-lg-10">
            <b-form-group name="name" description>
              <div class="row">
                <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="name">Имя</label>
                <div class="col-lg-10">
                  <b-form-input id="name" v-model="form.name" :state="validateName" type="text" required placeholder="Введите имя" autofocus :disabled="loading"></b-form-input>
                  <b-form-invalid-feedback :state="validateName">Некорректное имя. Должено быть длинее 2-х символов</b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <b-form-group name="surname" description>
              <div class="row">
                <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="surname">Фамилия</label>
                <div class="col-lg-10">
                  <b-form-input id="surname" v-model="form.surname" :state="validateSurname" type="text" placeholder="Введите фамилию" :disabled="loading"></b-form-input>
                  <b-form-invalid-feedback :state="validateSurname"></b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <b-form-group name="email" description>
              <div class="row">
                <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="email">Email</label>
                <div class="col-lg-10">
                  <b-form-input id="email" v-model="form.email" type="email" disabled></b-form-input>
                </div>
              </div>
            </b-form-group>

            <b-form-group name="phone" description>
              <div class="row">
                <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="phone">Телефон:</label>
                <div class="col-lg-10">
                  <b-form-input v-mask="'+7 (###) ###-##-##'" id="phone" v-model="form.phone" :state="validatePhone" type="text" placeholder="+7 (___) ___-__-__" :disabled="loading"></b-form-input>
                  <b-form-invalid-feedback :state="validatePhone">Некорректный телефон</b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <button type="button" v-b-toggle="'changePsw'" class="btn btn-link text-right">Сменить пароль</button>
            <b-collapse id="changePsw">
              <b-form-group name="password" description>
                <div class="row">
                  <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="password">Пароль:</label>
                  <div class="col-lg-10">
                    <b-form-input id="password" v-model="form.password" :state="validatePassword" type="password" placeholder="Введите пароль" :disabled="loading"></b-form-input>
                    <b-form-invalid-feedback :state="validatePassword">Некорректный пароль. Должен быть длинее 3-х символов</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>

              <b-form-group name="passwordConfirm" description>
                <div class="row">
                  <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="passwordConfirm">Пароль 2:</label>
                  <div class="col-lg-10">
                    <b-form-input id="passwordConfirm" v-model="form.passwordConfirm" :state="validatePasswordConfirm" type="password" placeholder="Подтвердите пароль" :disabled="loading"></b-form-input>
                    <b-form-invalid-feedback :state="validatePasswordConfirm">Пароли не совпадают</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>
            </b-collapse>

          </div>

        </div>

        <b-form-group>
          <div class="row">
            <div class="offset-md-3 col-md-7">
              <p-check name="category" color="info" v-model="form.company.isActive">Юридическое лицо</p-check>
            </div>
          </div>
        </b-form-group>

        <b-collapse id="companyForm" v-model="form.company.isActive">

          <b-form-group name="companyTitle" description>
            <div class="row">
              <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="companyTitle">Наименование:</label>
              <div class="col-lg-10">
                <b-form-input id="companyTitle" v-model="form.company.title" :state="validateСompanyTitle" type="text" placeholder="ИП Иванов И.И." :disabled="loading"></b-form-input>
                <b-form-invalid-feedback :state="validateСompanyTitle"></b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <b-form-group name="companyInn" description>
            <div class="row">
              <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="companyInn">ИНН:</label>
              <div class="col-lg-10">
                <b-form-input id="companyInn" v-model="form.company.inn" :state="validateСompanyInn" type="text" placeholder="0123456789" :disabled="loading"></b-form-input>
                <b-form-invalid-feedback :validateСompanyInn="true"></b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <b-form-group name="companyDescription" description>
            <div class="row">
              <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="companyDescription">Описание:</label>
              <div class="col-lg-10">
                <b-form-textarea id="companyDescription" v-model="form.company.description" :state="validateCompanyDescription" type="text" placeholder="Пару слов, чем вы занимаетесь" :disabled="loading"></b-form-textarea>
                <b-form-invalid-feedback :state="validateCompanyDescription"></b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <b-form-group name="companyAddress" description>
            <div class="row">
              <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="companyAddress">Адрес:</label>
              <div class="col-lg-10">
                <b-form-input id="companyAddress" v-model="form.company.address" :state="validateCompanyAddress" type="text" placeholder="ул. Центральная 1" :disabled="loading"></b-form-input>
                <b-form-invalid-feedback :state="validateCompanyAddress"></b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <div class="row">
            <label class="col-md-3 col-lg-2 col-form-label text-md-right" for="title">Файлы:</label>
            <div class="col-lg-10">
              <app-drag-drop-images :urls="user && user.company ? user.company.documents : []" :reset="resetFiles" @change-files="changeFiles" @remove-file="removeFile"></app-drag-drop-images>
            </div>
          </div>

        </b-collapse>

        <app-message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></app-message>
        <app-message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

        <b-form-group>
          <div class="row">
            <div class="offset-md-3 offset-lg-2 col-md-7 col-lg-10">
              <b-button type="submit" variant="outline-info" :disabled="loading">Сохранить</b-button>
            </div>
          </div>
        </b-form-group>

      </b-form>

    </div>

  </div>
</template>

<script lang="ts">
import userService from '../common/user.service'
import { User } from '../common/model/user.model'
import { capitalizeFirst, getErrTitles } from '../common/utils'

export default {
  name: 'app-profile',
  data() {
    return {
      loading: false,
      form: {
        name: '',
        surname: '',
        email: '',
        password: '',
        passwordConfirm: '',
        phone: '',
        company: {
          isActive: false,
          title: '',
          address: '',
          inn: '',
          description: ''
        }
      },
      avatarFile: undefined,
      companyFiles: [],
      companyFilesRemoved: [],
      messages: [],
      errMessages: [],
      resetFiles: null
    }
  },
  computed: {
    validateName() {
      return this.form.name.length > 1
    },
    validatePassword() {
      return this.form.password.length > 4 || !this.form.password.length
    },
    validatePasswordConfirm() {
      return this.form.passwordConfirm === this.form.password
    },
    validatePhone() {
      return !!this.form.phone.match(/^\+7\s[(][0-9]{3}[)]\s[0-9]{3}([-][0-9]{2}){2}$/) || !this.form.phone.length
    },
    validateSurname() {
      return this.form.surname.length > 4 || !this.form.surname.length
    },
    validateСompanyTitle() {
      return !this.form.company.isActive || (this.form.company.title && this.form.company.title.length > 4)
    },
    validateСompanyInn() {
      return !this.form.company.isActive || (this.form.company.inn && this.form.company.inn.length > 9 && this.form.company.inn.length < 13)
    },
    validateCompanyDescription() {
      return !this.form.company.isActive || (this.form.company.description && this.form.company.description.length > 4)
    },
    validateCompanyAddress() {
      return !this.form.company.isActive || (this.form.company.address && this.form.company.address.length > 4)
    },
    user(): User {
      return this.$store.getters.user
    },
    userName() {
      return capitalizeFirst(this.form.name) + ' ' + capitalizeFirst(this.form.surname)
    }
  },
  created() {
    this.form.name = this.user.name
    this.form.surname = this.user.surname
    this.form.email = this.user.email
    this.form.phone = this.user.phone
    this.form.company = this.user.company
  },
  methods: {
    async onSubmit(evt) {
      evt.preventDefault()
      this.loading = true
      this.clearMessages()
      const response = await userService.editUser(this.form.name, this.form.email, this.form.surname, this.form.phone, this.avatarFile, this.form.password, this.form.passwordConfirm, this.form.company, this.companyFiles, this.companyFilesRemoved)
      if (response.success) {
        // TODO: Don't call method again on get user data
        await this.$store.dispatch('GET_USER')
        // await this.$store.dispatch('SET_USER', response.data)
        this.messages = ['Профиль обновлён.']
        this.resetFiles = {}
      } else {
        this.errMessages = getErrTitles(response.error)
      }
      this.loading = false
    },
    onReset(evt) {
      evt.preventDefault()
      this.form.name = ''
      this.form.surname = ''
      this.form.email = ''
      this.form.password = ''
      this.form.passwordConfirm = ''
      this.form.phone = ''
    },
    changeAvatar(file) {
      this.avatarFile = file
      if (!file) {
        this.user.logo = ''
      }
    },
    changeFiles(files) {
      this.companyFiles = files
    },
    removeFile(file) {
      this.companyFilesRemoved.push(file)
    },

    dismissMessage(key) {
      this.messages.splice(key, 1)
    },
    dismissErr(key) {
      this.errMessages.splice(key, 1)
    },
    clearMessages() {
      this.messages = []
      this.errMessages = []
    }
  }
}
</script> 

<style lang="scss" scoped>

</style>
