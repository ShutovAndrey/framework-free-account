import ApiService from '../../services/api'
import Cookies from 'js-cookie'
import HTTP from './../../axios'

export const actions = {
  namespaced: true,
  state: {
    loggedIn: false,
    token: '',
    account: {},
    hasGift: false,
    gift: {}
  },
  mutations: {
    loginSuccess(state, data) {
      state.loggedIn = true
      const token = data.access_token
      Cookies.set('token', token, { expires: 0.5 })
      HTTP.defaults.headers.common.Authorization = 'Bearer ' + token
      state.token = token
    },
    fillAccount(state, data) {
      state.account = data
    },
    setToken(state, token) {
      state.token = token
    },
    takeGift(state, data) {
      state.hasGift = true
      state.gift = data
    },
    alreadyGifted(state) {
      state.hasGift = true
    },
    refuseGift(state) {
      state.gift = {}
      state.hasGift = false
    },
    convertGift(state, data) {
      state.gift = data
    },
    confirmGift(state) {
      state.gift.confirmed = true
    },
    reset(state) {
      state.loggedIn = false
      state.hasGift = false
      state.gift = {}
      state.token = ''
      state.account = {}
      Cookies.remove('token')
    }
  },
  actions: {
    signin({ commit }, data) {
      return new Promise((resolve, reject) => {
        ApiService.login(data).then(
          response => {
            commit('loginSuccess', response)
            resolve(response)
          },
          () => {
            commit('reset')
            reject()
          }
        )
      })
    },
    account({ commit }) {
      return new Promise((resolve, reject) => {
        ApiService.account().then(
          response => {
            commit('fillAccount', response)
            resolve(response)
          },
          () => {
            reject()
          }
        )
      })
    },

    takeGift({ commit }) {
      return new Promise((resolve, reject) => {
        ApiService.gift().then(
          response => {
            commit('takeGift', response)
            resolve(response)
          },
          () => {
            commit('alreadyGifted')
            reject()
          }
        )
      })
    },

    refuseGift({ commit }, data) {
      return new Promise((resolve, reject) => {
        ApiService.refuseGift(data).then(
          response => {
            commit('refuseGift')
            resolve(response)
          },
          () => {
            reject()
          }
        )
      })
    },

    convertGift({ commit }, data) {
      return new Promise((resolve, reject) => {
        ApiService.convertGift(data).then(
          response => {
            commit('convertGift', response)
            resolve(response)
          },
          () => {
            reject()
          }
        )
      })
    },

    confirmGift({ commit }, id) {
      return new Promise((resolve, reject) => {
        ApiService.confirmGift(id).then(
          response => {
            commit('confirmGift')
            resolve(response)
          },
          () => {
            reject()
          }
        )
      })
    },

    setToken({ commit }, token) {
      return new Promise(resolve => {
        commit('setToken', token)
        resolve()
      })
    },

    logout({ commit }) {
      return new Promise(resolve => {
        commit('reset')
        resolve()
      })
    }
  },
  getters: {
    loggedIn: state => !!state.loggedIn,
    token: state => state.token,
    points: state => state.account.points,
    name: state => state.account.name,
    uid: state => state.account.id,
    hasGift: state => state.hasGift,
    gift: state => state.gift
  }
}
