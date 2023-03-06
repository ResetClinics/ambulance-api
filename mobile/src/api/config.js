import axios from 'axios'
import AsyncStorage from '@react-native-async-storage/async-storage'

export default async () => {
  const baseURL = 'https://ambulance.rc-respect.ru/api/'
  const headers = {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  }
  const userToken = await AsyncStorage.getItem('userToken')
  if (userToken) {
    headers.Authorization = `Bearer ${userToken}`
  }

  const axiosInstance = axios.create({
    baseURL,
    headers,
  })

  axiosInstance.interceptors.response.use(
    ({ data }) => new Promise((resolve) => {
      resolve(data)
    }),
    (error) => {
      if (!error.response) {
        return new Promise((resolve, reject) => {
          reject(error)
        })
      }
      console.log(error)
    },
  )

  return axiosInstance
}
