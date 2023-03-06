import React, { useState } from 'react'
import { FlatList, RefreshControl } from 'react-native'
import {
  BottomNavigation, CardItem, Layout, ScreenLayout
} from '../../components'
import { data } from '../../data/data'
import { COLORS } from '../../../constants'

export const CallHistory = ({ navigation }) => {
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
              colors={['#04607A']}
            />
          )}
          /* eslint-disable-next-line react/jsx-props-no-spreading */
          renderItem={({ item }) => <CardItem {...item} goToMapPage={goToMapPage} text="Свернуть" />}
        />
      </Layout>
      <BottomNavigation navigation={navigation} />
    </ScreenLayout>
  )
}
