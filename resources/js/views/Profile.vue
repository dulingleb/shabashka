<template>
<div class="">
  {{ userProfile }}

  <p>Отзывы</p>
  {{ reviews }}
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
      userProfile: null,
      reviews: null,
      errReponseMessages: [],
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
      return
    }

    const reviewsResponse = await userService.getReviews(this.userProfile.id)
    this.reviews = reviewsResponse.reviews
    if (reviewsResponse.total === -1) {
      this.errReponseMessages.push('Возможно проблемы с интернет-соединением. Перезагрузите страницу.')
    }

  },
  methods: {

  }
}
</script> 

<style lang="scss" scoped>

</style>
