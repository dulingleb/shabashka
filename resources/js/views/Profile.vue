<template>
<div class="row justify-content-center">

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
