 <template>
  <div class="logo" :style="{ backgroundColor: userColor }">

    <span class="user-title">{{ userTitle  }}</span>

    <div v-if="image" class="img">
      <img src="https://via.placeholder.com/300x350" alt />
    </div>

    <label class="btn change-avatar">
      <span class="title">Сменить аватар</span>
      <input type="file" accept="image/*">
    </label>

  </div>
</template>

<script lang="ts">

import { stringToHslColor } from '../common/utils'

export default {
  name: 'avatar',
  components: {},
  props: ['userName', 'image'],
  data() {
    return {
      userColor: '#17a2b8',
      userTitle: ''
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

  // tslint:disable-next-line:no-empty
  mounted() {
    this.setUserColor()
  },

  methods: {
    setUserColor(): void {
      this.userColor = stringToHslColor(this.userName, 30, 80)
      if (this.userName && this.userName.length) {
        this.userTitle = this.userName[0]
      }
    }
  }
}
</script> 

<style lang="scss" scoped>
.logo {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  border-radius: 50%;
  overflow: hidden;
  &:after {
    content: "";
    display: block;
    padding-bottom: 100%;
  }
  .user-title {
    margin-top: -10%;
    font-size: 700%;
    color: #fff;
    line-height: 0;
    opacity: 1;
    transition: 0.4s;
  }
  .logo-wrapp {
    text-transform: uppercase;
    font-size: 20px;
  }
  .change-avatar {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #333;
    font-size: 115%;
    font-weight: bold;
    cursor: pointer;
    opacity: 0;
    transition: 0.4s;

    input[type=file] {
      display: none;
    }
  }

  &:hover {
    .user-title {
      opacity: 0.2;
    }
    .change-avatar {
      opacity: 0.8;
    }
  }
}

.img {
  border-radius: 4px;
  overflow: hidden;

  img {
    width: 100%;
  }
}


</style>