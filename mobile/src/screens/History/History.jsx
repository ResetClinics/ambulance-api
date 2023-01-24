import React, { useState } from 'react'
import { FlatList, RefreshControl, StyleSheet } from 'react-native'
import {
  BottomNavigation, CardItem, Layout, ScreenLayout
} from '../../components'
import { data } from '../../data/data'
import { COLORS } from '../../../constants'

export const History = ({ navigation }) => {
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
            />
          )}
          /* eslint-disable-next-line react/jsx-props-no-spreading */
          renderItem={({ item }) => <CardItem {...item} style={styles.default} status="Вызов завершен" goToMapPage={goToMapPage} text="Свернуть" />}
        />
      </Layout>
      <BottomNavigation navigation={navigation} />
    </ScreenLayout>
  )
}

const styles = StyleSheet.create({
  default: {
    borderColor: COLORS.border
  }
})
