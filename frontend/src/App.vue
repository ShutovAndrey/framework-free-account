<template>
  <div id="app">
    <div v-if="token" class="container">
      <el-button type="info" plain class="logout" @click="logout"
        >Log out</el-button
      >
      <div>
        Hi, {{ name }}! <br />your loyalty points balance:
        <span :style="{ color: '#E6A23C' }">
          {{ points }}
        </span>
      </div>
    </div>
    <router-view />
  </div>
</template>

<script>
import HTTP from './axios'
import Cookies from 'js-cookie'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'App',

  computed: {
    ...mapGetters('actions', ['token', 'points', 'name'])
  },
  methods: {
    ...mapActions('actions', ['account', 'setToken', 'logout'])
  },
  created() {
    if (!this.token && Cookies.get('token')) {
      this.setToken(Cookies.get('token'))
    }
  },
  watch: {
    token() {
      if (this.token) {
        HTTP.defaults.headers.common.Authorization = 'Bearer ' + this.token
        this.account()
          .then(() => {})
          .catch(() => {})
      } else {
        this.$router.push('login')
      }
    }
  }
}
</script>

<style lang="scss" scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 30px;
}
.container {
  display: flex;
  margin-top: 100px;
  .logout {
    margin-right: auto;
  }
}
</style>
