import React, { useState } from 'react'
import { FlatList, RefreshControl, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import {
  BottomNavigation, CardItem, Layout, ScreenLayout
} from '../../components'
import { data } from '../../data/data'
import { COLORS } from '../../../constants'

export const Calls = ({ navigation }) => {
  const [refreshing, setRefreshing] = useState(false)
  const goToMapPage = () => {
    navigation.navigate('itinerary', {
      screen: 'Home',
      params: {
        screen: 'itinerary',
      },
    })
  }
  const onRefresh = () => {
    setRefreshing(true)
    setTimeout(() => {
      setRefreshing(false)
    }, 1500)
  }
  const onAccepting = () => {
    navigation.navigate('сurrentCall')
  }

  // eslint-disable-next-line react/no-unstable-nested-components
  const ViewCard = ({ item, active }) => {
    if (active === true) {
      return (
        // eslint-disable-next-line react/jsx-props-no-spreading
        <CardItem {...item} style={styles.color} status="" onAccepting={onAccepting} goToMapPage={goToMapPage} text="Свернуть"><Button onPress={() => onAccepting()}>Принять</Button></CardItem>
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
          data={data}
          refreshControl={(
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              tintColor={COLORS.primary}
            />
          )}
          renderItem={({ item }) => <ViewCard item={item} active={item.active} />}
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
