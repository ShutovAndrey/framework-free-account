import axios from 'axios'

const baseDomain = process.env.VUE_APP_API_HOST || '/'

const HTTP = axios.create({
  baseURL: baseDomain
})

export default HTTP
