import React from 'react'
import { Button } from 'react-native-paper'
import { FlatList } from 'react-native'
import { CardItem, Layout } from '../../components'
import { data } from '../../data/data'

export const Notifications = ({ navigation }) => {
  const onAccepting = () => {
    navigation.navigate('Текущий вызов')
  }

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
        showsVerticalScrollIndicator={false}
        data={data}
        /* eslint-disable-next-line react/jsx-props-no-spreading */
        renderItem={({ item }) => <CardItem {...item} status="" onAccepting={onAccepting} goToMapPage={goToMapPage} text="Позвонить заказчику"><Button onPress={() => onAccepting()}>Принять</Button></CardItem>}
      />
    </Layout>
  )
}
