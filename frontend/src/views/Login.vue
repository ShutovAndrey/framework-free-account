<template>
  <div class="rate-card-page">
    <el-form
      :model="form"
      ref="form"
      label-width="120px"
      :style="{ width: `500px`, margin: 'auto' }"
    >
      <el-form-item
        prop="email"
        label="Email"
        :rules="[
          {
            required: true,
            message: 'Please input email address',
            trigger: 'blur'
          },
          {
            type: 'email',
            message: 'Please input correct email address',
            trigger: ['blur', 'change']
          }
        ]"
      >
        <el-input v-model="form.email"></el-input>
      </el-form-item>
      <el-form-item
        prop="password"
        label="Password"
        :rules="[
          {
            required: true,
            message: 'Please input password',
            trigger: 'blur'
          }
        ]"
      >
        <el-input type="password" v-model="form.password"></el-input>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="submit()">Submit</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  data: () => ({
    form: {
      email: '',
      password: ''
    }
  }),
  created() {},
  computed: {},
  methods: {
    ...mapActions('actions', ['signin']),
    submit() {
      this.$refs['form'].validate(valid => {
        if (!valid) {
          this.$notify.error({
            title: 'Error',
            message: 'Please fill the form correctly'
          })
          return false
        } else {
          this.signin(this.form)
            .then(() => {
              this.$notify({
                title: 'Success',
                message: 'Welcome',
                type: 'success'
              })
            })
            .finally(() => this.$router.push('panel'))
            .catch(() => {
              this.$notify.error({
                title: 'Error',
                message: 'Invalid credentials'
              })
            })
        }
      })
    }
  }
}
</script>
