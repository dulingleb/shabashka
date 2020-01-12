<template>
<div class="">
  {{ userProfile }}
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
      userProfile: null
    }
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    userName() {
      return capitalizeFirst(this.form.name) + ' ' + capitalizeFirst(this.form.surname)
    }
  },
  async mounted() {
    this.userProfile = await userService.getUserById(this.$route.params.id)
    if (!this.userProfile) {
      this.$router.push('/')
    }
  },
  methods: {

  }
}
</script> 

<style lang="scss" scoped>

</style>
