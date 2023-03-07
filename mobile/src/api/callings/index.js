import axiosInstance from '../config'

const callings = {
  async index() {
    const axios = await axiosInstance()
    return axios.get(
      'callings'
    )
  },
}

export { callings }
