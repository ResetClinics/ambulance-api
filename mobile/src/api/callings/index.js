import axiosInstance from '../config'

const callings = {
  async index(team = null) {
    const axios = await axiosInstance()
    return axios.get(
      `callings?team=${team}`
    )
  },

  async current() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/current',
      {}
    )
  },
  async accept(id) {
    const axios = await axiosInstance()
    return axios.post(
      `callings/${id}/accept`,
      {}
    )
  },
  async arrive() {
    const axios = await axiosInstance()
    return axios.post(
      'callings/arrive',
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
  async reject(id) {
    const axios = await axiosInstance()
    return axios.post(
      `callings/${id}/reject`,
      {}
    )
  },
}

export { callings }
