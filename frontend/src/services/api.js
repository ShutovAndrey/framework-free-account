import HTTP from '../axios'

class ApiService {
  async login(data) {
    return HTTP.post('/auth', data).then(response => {
      if (response.status === 201) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }

  async account(data) {
    return HTTP.get('/account', data).then(response => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }

  async gift() {
    return HTTP.get('/gift').then(response => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }

  async refuseGift(id) {
    return HTTP.delete(`/gift/${id}`).then(response => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }

  async convertGift(id) {
    return HTTP.put(`/gift/${id}`).then(response => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }

  async confirmGift(id) {
    return HTTP.put(`/gift/${id}/confirm`).then(response => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(response)
    })
  }
}

export default new ApiService()
