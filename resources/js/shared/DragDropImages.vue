 <template>
  <div class="">
    <div class="drag-wrapp">
      <div class="drag-block">
        <div class="title-wrapp">
          <label class="drag-title">Перетащите или выбирете файлы</label>
        </div>
        <input ref="fileinput" type="file" @change="previewFiles" multiple>

        <div class="preview-wrapp">
          <div v-for="(file, key) in files" class="file-listing">
            <div class="img-wrapp">
              <img class="img" :ref="'preview' + key"/>
            </div>
            <p class="image-name">{{ file.name }}</p>
            <div class="remove-container">
              <button class="btn btn-danger btn-remove" type="button" @click="removeFile( key )">Удалить</button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script lang="ts">

export default {
  name: 'drag-drop-images',
  components: {},
  data() {
    return {
      dragAndDropCapable: false,
      files: [],
      uploadPercentage: 0
    }
  },

  // tslint:disable-next-line:no-empty
  mounted() { },

  methods: {

    previewFiles() {
      const filesInput = this.$refs.fileinput.files
      for (let i = 0; i < filesInput.length; i++) {
        console.log(filesInput[i])
        this.files.push(filesInput[i])
        this.getImagePreviews()
      }
    },
    getImagePreviews() {
      for (let i = 0; i < this.files.length; i++) {
        if (/\.(jpe?g|png|gif)$/i.test(this.files[i].name)) {
          let reader = new FileReader()
          reader.addEventListener('load', function() {
            this.$refs['preview' + i][0].src = reader.result
          }.bind(this), false)
          reader.readAsDataURL(this.files[i])
        } else {
          this.$nextTick(() => { this.$refs['preview' + i][0].src = '/images/file.png' })
        }
      }
    },

    removeFile(key) {
      this.files.splice(key, 1)
    }
  }
}
</script> 

<style lang="scss" scoped>
  .drag-wrapp {
    padding-bottom: 15px;
  }
  .drag-block {
    position: relative;
    min-height: 250px;
    background: #f7f7f7;
    .drag-title {

    }
    input, .title-wrapp {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
    }
    input {
      opacity: 0;
      z-index: 2;
      cursor: pointer;
    }
    .title-wrapp {
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }

  .preview-wrapp {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    .file-listing {
      margin: 10px;
      text-align: center;
      overflow: hidden;
      .img-wrapp {
        width: 150px;
        height: 150px;
        .img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
      .image-name {
        font-size: 12px;
      }
      .remove-container {
        position: relative;
        z-index: 2;
        .btn-remove {

        }
      }
    }
  }
</style>