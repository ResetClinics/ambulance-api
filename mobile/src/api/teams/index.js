import axiosInstance from '../config'

const teams = {
  async my() {
    const axios = await axiosInstance()
    return await axios.get(
      'teams/my',
    )
  },
  async reject() {
    const axios = await axiosInstance()
    return await axios.post(
      'teams/reject',
      {administrator: '/api/users/1'}
    )
  },
  async accept() {
    const axios = await axiosInstance()
    return await axios.post(
      'teams/accept',
      {administrator: '/api/users/1'}
    )
  },
  async complete() {
    const axios = await axiosInstance()
    return await axios.post(
      'teams/complete',
      {administrator: '/api/users/1'}
    )
  }
}

export { teams }
