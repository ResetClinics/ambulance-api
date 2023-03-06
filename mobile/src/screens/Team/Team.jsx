import React, { useEffect, useState } from 'react'
import {
  StyleSheet, Text,
} from 'react-native'
import {
  BottomNavigation, ScreenLayout,
  TeamLayout,
} from '../../components'
import { API } from '../../api'
import { COLORS } from '../../../constants'
import { TeamAssigned } from './TeamAssigned'
import { TeamAccepted } from './TeamAccepted'

export const Team = ({ navigation }) => {
  const [refreshing, setRefreshing] = useState(false)
  const [team, setTeam] = useState({})
  const [status, setStatus] = useState('not_assigned') // Status.notAssigned

  useEffect(() => {
    fetchMyTeam()
  }, [])

  const Status = {
    notAssigned: 'not_assigned',
    assigned: 'assigned',
    accepted: 'accepted'
  }

  const fetchMyTeam = async () => {
    setRefreshing(true)
    try {
      const response = await API.teams.my()
      const { status } = response
      setTeam(response)
      setStatus(status)
    } catch (error) {
      setStatus('not_assigned')
      setTeam({})
    }
    setRefreshing(false)
  }

  const rejectTeam = async () => {
    setRefreshing(true)
    await API.teams.reject()
    await fetchMyTeam()
  }

  const acceptTeam = async () => {
    setRefreshing(true)
    await API.teams.accept()
    await fetchMyTeam()
  }

  const completeTeam = async () => {
    setRefreshing(true)
    await API.teams.complete()
    await fetchMyTeam()
  }

  if (status === Status.assigned && team) {
    const { administrator, doctors } = team
    return (
      <ScreenLayout>
        <TeamAssigned
          administrator={administrator}
          doctors={doctors}
          refreshing={refreshing}
          onRefresh={fetchMyTeam}
          acceptTeam={acceptTeam}
          rejectTeam={rejectTeam}
        />
        <BottomNavigation navigation={navigation} />
      </ScreenLayout>
    )
  }

  if (status === Status.accepted && team) {
    return (
      <ScreenLayout>
        <TeamAccepted
          {...team}
          refreshing={refreshing}
          onRefresh={fetchMyTeam}
          completeTeam={completeTeam}
        />
        <BottomNavigation navigation={navigation} />
      </ScreenLayout>
    )
  }
  return (
    <>
      <TeamLayout refreshing={refreshing} onRefresh={fetchMyTeam}>
        <Text style={styles.text}>Бригада еще не сформирована</Text>
      </TeamLayout>
      <BottomNavigation navigation={navigation} />
    </>
  )
}

const styles = StyleSheet.create({
  text: {
    fontSize: 30,
    color: COLORS.black,
    fontFamily: 'Roboto-Medium',
    lineHeight: 40
  },
})
