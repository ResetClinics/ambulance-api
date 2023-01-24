import {
  RefreshControl, ScrollView, StyleSheet, Text
} from 'react-native'
import React, { useState } from 'react'
import { COLORS } from '../../../../constants'
import { Layout } from '../../../components'

export const TeamNotAssigned = ({ navigation }) => {
  const [refreshing, setRefreshing] = useState(false)

  const onRefresh = () => {
    setRefreshing(true)
    setTimeout(() => {
      setRefreshing(false)
      navigation.navigate('Состав Бригады')
    }, 1500)
  }
  return (
    <Layout>
      <ScrollView
        showsVerticalScrollIndicator={false}
        refreshControl={(
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor={COLORS.primary}
          />
        )}
      >
        <Text style={styles.text}>Бригада еще не сформирована</Text>
      </ScrollView>
    </Layout>
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
