import React, { createContext, useEffect, useState } from 'react'
import AsyncStorage from '@react-native-async-storage/async-storage'
import jwtDecode from 'jwt-decode'
import Container, { Toast } from 'toastify-react-native'
import { API } from '../api'

export const AuthContext = createContext()

export const AuthProvider = ({ children }) => {
  const [isLoading, setIsLoading] = useState(false)
  const [userToken, setUserToken] = useState(null)
  const [userInfo, setUserInfo] = useState(null)

  const login = async ({ phone, password }) => {
    const showToasts = () => {
      Toast.error('Ошибка получения данных')
    }
    setIsLoading(true)
    const response = await API.auth.login(phone, password)
    if (!response) {
      setIsLoading(false)
      showToasts()
      return
    }
    const { token } = response
    const userInfo = jwtDecode(token)
    setUserInfo(userInfo)
    setUserToken(token)

    AsyncStorage.setItem('userInfo', JSON.stringify(userInfo))
    AsyncStorage.setItem('userToken', token)
    setIsLoading(false)
  }

  const logout = () => {
    setIsLoading(true)
    setUserToken(null)
    AsyncStorage.removeItem('userInfo')
    AsyncStorage.removeItem('userToken')
    setIsLoading(false)
  }

  const isLoggedIn = async () => {
    try {
      setIsLoading(true)
      let userInfo = await AsyncStorage.getItem('userInfo')
      const userToken = await AsyncStorage.getItem('userToken')
      userInfo = JSON.parse(userInfo)

      if (userInfo) {
        setUserToken(userToken)
        setUserInfo(userInfo)
      }
      setUserToken(userToken)
      setIsLoading(false)
    } catch (error) {
      console.log(error)
    }
  }
  useEffect(() => {
    isLoggedIn()
  }, [])
  return (
    <AuthContext.Provider value={{
      login, logout, isLoading, userToken
    }}
    >
      <Container position="top" />
      {children}
    </AuthContext.Provider>
  )
}
