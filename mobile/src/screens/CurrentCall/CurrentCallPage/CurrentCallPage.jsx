import React, { useContext } from 'react'
import { AcceptedCall, Call } from '../../../components'
import { CurrentCallingContext } from '../../../context/CurrentCallingContext'

export const CurrentCallPage = ({ navigation }) => {
  const { currentCalling, arrive, complete } = useContext(CurrentCallingContext)
  const { status } = currentCalling

  const goToMapPage = () => {
    navigation.navigate('itinerary')
  }


  if (status === 'accepted') {
    return <Call navigation={goToMapPage} onArrival={arrive} />
  }
  return <AcceptedCall onAccepting={complete} />

}
