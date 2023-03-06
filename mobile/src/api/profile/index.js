import axiosInstance from '../config'

const profile = {
  async user({ id }) {
    const axios = await axiosInstance()
    return await axios.get(
      `users/${id}`,
    )
  }
}

export { profile }
