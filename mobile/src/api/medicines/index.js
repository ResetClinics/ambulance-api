import axiosInstance from '../config'

const medicines = {
  async index() {
    const axios = await axiosInstance()
    return axios.get(
      'medicines'
    )
  },
}

export { medicines }
