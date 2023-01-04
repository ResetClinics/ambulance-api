import React, { useState } from 'react'
import { AcceptedCall, Call } from '../../../components'

export const CurrentCallPage = ({ navigation }) => {
  const [arrival, setArrival] = useState(false)
  const goToMapPage = () => {
    navigation.navigate('Маршрут')
  }
  const onArrival = () => {
    setArrival(true)
  }

  const onAccepting = () => {
    navigation.navigate('Уведомления')
  }

  if (arrival) {
    return <AcceptedCall onAccepting={onAccepting} />
  }
  return <Call navigation={goToMapPage} onArrival={onArrival} />
}
