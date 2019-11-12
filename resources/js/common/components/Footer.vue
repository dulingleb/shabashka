 <template>
  <footer>
      <div class="container">
        <p class="copyright text-center"><strong>shabashki.ru.</strong> Copyright © 2019</p>
      </div>
  </footer>
</template>

<script lang="ts">
export default {
  name: 'app-footer',
  components: {}
}
</script> 

<style lang="scss" scoped>
  footer {
    background: #f7f7f7;
    box-shadow: inset 4px 24px 23px -34px rgba(0,0,0,0.2);
    .copyright {
      margin: 0;
      padding: 20px 10px;
    }
  }
</style>