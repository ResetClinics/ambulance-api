import React, { useEffect, useState } from 'react'
import { FlatList, RefreshControl, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import {
  BottomNavigation, CardItem, Layout, ScreenLayout
} from '../../components'
import { COLORS } from '../../../constants'
import { API } from '../../api'

export const Calls = ({ navigation }) => {
  const [refreshing, setRefreshing] = useState(false)
  const [callings, setCallings] = useState([])
  const goToMapPage = () => {
    navigation.navigate('itinerary', {
      screen: 'Home',
      params: {
        screen: 'itinerary',
      },
    })
  }

  useEffect(() => {
    fetchCallings()
  }, [])

  const fetchCallings = async () => {
    setRefreshing(true)
    try {
      const response = await API.callings.index()
      setCallings(response)
    } catch (error) { /* empty */ }
    setRefreshing(false)
  }

  const onAccepting = () => {
    navigation.navigate('сurrentCall')
  }

  // eslint-disable-next-line react/no-unstable-nested-components
  const ViewCard = ({ item }) => {
    const { status } = item

    if (status === 'assigned') {
      return (
        // eslint-disable-next-line react/jsx-props-no-spreading
        <CardItem {...item} style={styles.color} status="" onAccepting={onAccepting} goToMapPage={goToMapPage} text="Свернуть">
          <Button onPress={() => onAccepting()}>Принять</Button>
        </CardItem>
      )
    }
    return (
      // eslint-disable-next-line react/jsx-props-no-spreading
      <CardItem {...item} style={styles.default} status="" goToMapPage={goToMapPage} text="Свернуть" />

    )
  }
  return (
    <ScreenLayout>
      <Layout>
        <FlatList
          showsVerticalScrollIndicator={false}
          data={callings}
          refreshControl={(
            <RefreshControl
              refreshing={refreshing}
              onRefresh={fetchCallings}
              tintColor={COLORS.primary}
              colors={['#04607A']}
            />
          )}
          renderItem={({ item }) => <ViewCard item={item} />}
        />
      </Layout>
      <BottomNavigation navigation={navigation} />
    </ScreenLayout>
  )
}

const styles = StyleSheet.create({
  color: {
    borderColor: COLORS.primary
  },
  default: {
    borderColor: COLORS.border
  }
})
