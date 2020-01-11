 <template>
  <div>
    <b-alert show dismissible fade variant="success" v-if="message" @dismissed="dismissed">{{ message }}</b-alert>
    <b-alert show dismissible fade variant="danger" v-if="error" @dismissed="dismissed">{{ error }}</b-alert>
  </div>
</template>

<script lang="ts">

import { stringToHslColor, isImage } from '../../common/utils'

export default {
  name: 'app-message',
  components: {},
  props: ['message', 'error'],
  data() {
    return {
    }
  },
  watch: {
    userName() {
      this.setUserColor()
    },
    image() {
      this.setUserColor()
    }
  },

  methods: {
    dismissed() {
      this.$emit('dismiss')
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>