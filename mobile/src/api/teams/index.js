import axiosInstance from '../config'

const teams = {
  async my() {
    const axios = await axiosInstance()
    const ress = await axios.post(
      'teams/my',
      {}
    )

    return ress
  },
  async reject() {
    const axios = await axiosInstance()
    return axios.post(
      'teams/reject',
      {}
    )
  },
  async accept() {
    const axios = await axiosInstance()
    return await axios.post(
      'teams/accept',
      {}
    )
  },
  async complete() {
    const axios = await axiosInstance()
    return await axios.post(
      'teams/complete',
      {}
    )
  }
}

export { teams }
