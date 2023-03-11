import axiosInstance from '../config'

const callings = {
  async index() {
    const axios = await axiosInstance()
    return axios.get(
      'callings'
    )
  },

  async current() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/current',
      {}
    )
  },
  async accept() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/accept',
      {}
    )
  },
  async arrive() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/accept',
      {}
    )
  },
  async complete() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/complete',
      {}
    )
  },
  async reject() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/reject',
      {}
    )
  },
}

export { callings }
