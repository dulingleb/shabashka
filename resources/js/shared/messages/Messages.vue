 <template>
  <div>
      <template v-if="messages">
        <app-message v-for="(message, key) in messages" :key="key" :message="!isError(message) && message.text" :error="isError(message) && message.text" @dismiss="dismissMessage(key)"></app-message>
      </template>
  </div>
</template>

<script lang="ts">

import { stringToHslColor, isImage } from '../../common/utils'

export default {
  name: 'app-messages',
  components: {},
  props: ['messages'],

  methods: {
    isError(message) {
      return message.variant === 'danger'
    },

    dismissMessage(key) {
      this.$emit('change-messages', this.messages)
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>