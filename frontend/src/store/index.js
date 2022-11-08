import Vue from 'vue'
import Vuex from 'vuex'
import { actions } from './modules/actions'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    actions
  }
})
