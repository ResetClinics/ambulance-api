import React from 'react'
import { FlatList } from 'react-native'
import {
  BottomNavigation, CardItem, Layout, ScreenLayout
} from '../../components'
import { data } from '../../data/data'

export const CallHistory = ({ navigation }) => {
  const goToMapPage = () => {
    navigation.navigate('itinerary', {
      screen: 'Home',
      params: {
        screen: 'itinerary',
      },
    })
  }
  return (
    <ScreenLayout>
      <Layout>
        <FlatList
          showsVerticalScrollIndicator={false}
          data={data}
          /* eslint-disable-next-line react/jsx-props-no-spreading */
          renderItem={({ item }) => <CardItem {...item} goToMapPage={goToMapPage} text="Свернуть" />}
        />
      </Layout>
      <BottomNavigation navigation={navigation} />
    </ScreenLayout>
  )
}
