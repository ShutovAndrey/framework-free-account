<template>
  <div class="actions">
    <div v-if="Object.keys(gift).length === 0 && !confirmed">
      <Button :disabled="hasGift" type="success" @click="getGift()"
        >Get some gift!</Button
      >
    </div>
    <div v-else-if="!confirmed">
      <div>Your gift is {{ giftEntity }}</div>
      <Button type="info" @click="refuse()">I don't need your gifts</Button>
      <Button type="success" @click="confirm()">Confirm Gift</Button>
      <Button type="info" @click="convert()" v-if="gift.type == 1"
        >Converts to loyalty points</Button
      >
    </div>
    <div v-else>Have a nice day!</div>
  </div>
</template>
s

<script>
import { mapGetters, mapActions } from 'vuex'

import { Button } from 'element-ui'

export default {
  components: {
    Button
  },
  data: () => ({
    confirmed: false
  }),
  computed: {
    ...mapGetters('actions', ['uid', 'hasGift', 'gift']),
    giftEntity() {
      return {
        1: `${this.gift.amount}$ cache`,
        2: `${this.gift.points} loyalty points points`,
        3: `${this.gift.good}`
      }[this.gift.type]
    }
  },
  methods: {
    ...mapActions('actions', [
      'takeGift',
      'refuseGift',
      'convertGift',
      'account',
      'confirmGift'
    ]),
    getGift() {
      this.takeGift(this.uid)
        .then(() => {
          this.$notify({
            title: 'Success',
            message: 'Have a nice day',
            type: 'success'
          })
        })
        .catch(() => {
          this.$notify.error({
            title: 'Error',
            message: "Seems you're already have a gift"
          })
          this.confirmed = true
        })
    },
    refuse() {
      this.refuseGift(this.gift.id)
        .then(() => {
          this.$notify({
            title: 'Success',
            message: 'As you wish',
            type: 'success'
          })
        })
        .catch(() => {})
    },
    convert() {
      this.convertGift(this.gift.id)
        .then(() => (this.confirmed = true))
        .then(() => {
          this.$notify({
            title: 'Success',
            message: 'As you wish',
            type: 'success'
          })
        })
        .finally(() => this.account())
        .catch(() => {})
    },
    confirm() {
      this.confirmGift(this.gift.id)
        .then(() => (this.confirmed = true))
        .then(() => {
          this.$notify({
            title: 'Success',
            message: 'Well done!',
            type: 'success'
          })
        })
        .then(() => this.account())
        .catch(() => {})
    }
  }
}
</script>

<style lang="scss" scoped>
.actions {
  margin-top: 20px;
  .el-button {
    margin-top: 10px;
  }
}
</style>
