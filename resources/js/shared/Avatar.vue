 <template>
  <div class="logo" :class="{ edited: isEdit }" :style="{ backgroundColor: userColor }">

    <span class="user-title">{{ userTitle  }}</span>

    <div v-if="image && !filePreview" class="img">
      <img :src="image" alt />
    </div>

    <template v-if="isEdit">
      <div v-if="!image && filePreview" class="img">
        <img :src="filePreview" alt />
      </div>

      <label v-if="!image && !filePreview" class="btn change-avatar" title="Откройте или перетащите изображение">
        <font-awesome-icon :icon="['fas', 'folder-open']" class="icon" />
        <input type="file" accept="image/*" ref="fileinput" @change="previewFile">
      </label>

      <font-awesome-icon v-if="image || filePreview" :icon="['fas', 'times']" @click="removeAvatar" class="icon-remove" />
    </template>

  </div>
</template>

<script lang="ts">

import { stringToHslColor, isImage } from '../common/utils'

export default {
  name: 'avatar',
  components: {},
  props: ['userName', 'image', 'isEdit'],
  data() {
    return {
      userColor: '#17a2b8',
      userTitle: '',
      file: null,
      filePreview: ''
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
    },
    previewFile() {
      const filesInput = this.$refs.fileinput.files
      if (filesInput && filesInput.length && isImage(filesInput[0].name)) {
        this.file = filesInput[0]
        this.$emit('change-file', this.file)
        this.getImagePreview()
      }
    },
    removeFile() {
      this.file = null
      this.filePreview = ''
      this.$emit('change-file', this.file)
    },
    getImagePreview() {
      let reader = new FileReader()
      reader.addEventListener('load', function() {
        this.filePreview = reader.result
      }.bind(this), false)
      reader.readAsDataURL(this.file)
    },

    removeAvatar() {
      this.image = ''
      this.removeFile()
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

    input[type=file] {
      display: none;
    }
  }

  .icon-remove {
    position: absolute;
  }

  .img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }
  }

  .user-title, .img {
    opacity: 1;
    transition: 0.4s;
  }

  .change-avatar, .icon-remove {
    color: #333;
    font-size: 715%;
    font-weight: bold;
    opacity: 0;
    transition: 0.6s;
    cursor: pointer;
  }

  &.edited {
    &:hover {
      .user-title, .img {
        opacity: 0.1;
      }
      .change-avatar, .icon-remove {
        opacity: 0.5;
      }
    }
  }
}




</style>