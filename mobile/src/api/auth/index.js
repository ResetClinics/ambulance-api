import axiosInstance from '../config'

const auth = {
  async login(phone, password) {
    const axios = await axiosInstance()
    return await axios.post(
      'login_check',
      {
        phone: phone.replace(/\D/g, ''),
        password
      },
    )
  }
}

export { auth }
