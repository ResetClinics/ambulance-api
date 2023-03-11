import React, {createContext, useEffect, useState} from 'react'
import {API} from '../api'

export const CurrentCallingContext = createContext()

export const CurrentCallingProvider = ({children}) => {

  const [currentCalling, setCurrentCalling] = useState(null)

  const fetchCurrentCalling = async () => {
    const response = await API.callings.current()
    setCurrentCalling(response)
  }

  const accept = async (id) => {
    await API.callings.accept(id)
    const current = await API.callings.current()
    setCurrentCalling(current)
  }

  const arrive = async () => {
    await API.callings.arrive()
    const current = await API.callings.current()
    setCurrentCalling(current)
  }

  const complete = async () => {
    await API.callings.complete()
    const current = await API.callings.current()
    setCurrentCalling(current)
  }

  useEffect(() => {
    fetchCurrentCalling()
  }, [])

  return (
    <CurrentCallingContext.Provider value={{
      currentCalling, fetchCurrentCalling, arrive, complete, accept
    }}
    >
      {children}
    </CurrentCallingContext.Provider>
  )
}
