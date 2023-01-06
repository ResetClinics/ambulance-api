import React from 'react'
import { FlatList } from 'react-native'
import { CardItem, Layout } from '../../components'
import { data } from '../../data/data'

export const CallHistory = ({ navigation }) => {
  const goToMapPage = () => {
    navigation.navigate('Маршрут', {
      screen: 'Home',
      params: {
        screen: 'Маршрут',
      },
    })
  }
  return (
    <Layout>
      <FlatList
        data={data}
        /* eslint-disable-next-line react/jsx-props-no-spreading */
        renderItem={({ item }) => <CardItem {...item} goToMapPage={goToMapPage} text="Свернуть" />}
      />
    </Layout>
  )
}
