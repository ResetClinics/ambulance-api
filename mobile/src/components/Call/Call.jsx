import {
  RefreshControl,
  ScrollView, StyleSheet, Text, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React, { useContext, useState } from 'react'
import { Layout } from '../Layout'
import { CardLayout } from '../CardLayout'
import { COLORS, FONTS } from '../../../constants'
import { MapMarkerIcon } from '../MapMarkerIcon'
import { CloseIcon } from '../CloseIcon'
import { CurrentCallingContext } from '../../context/CurrentCallingContext'
import { formatDate, formatTime } from '../../helpers'

export const Call = ({ navigation, onArrival }) => {

  const { currentCalling, fetchCurrentCalling } = useContext(CurrentCallingContext)

  const {
    address, createdAt, description
  } = currentCalling

  const dateTime = new Date(createdAt)

  const createdDate = formatDate(dateTime)
  const createdTime = formatTime(dateTime)

  const [refreshing, setRefreshing] = useState(false)

  const onRefresh = () => {
    setRefreshing(true)
    fetchCurrentCalling()
    setTimeout(() => {
      setRefreshing(false)
    }, 1500)
  }
  return (
    <ScrollView
      style={styles.root}
      showsVerticalScrollIndicator={false}
      refreshControl={(
        <RefreshControl
          refreshing={refreshing}
          onRefresh={onRefresh}
          tintColor={COLORS.primary}
          colors={['#04607A']}
        />
      )}
    >
      <Layout>
        <CardLayout address={address} subject="" date={createdDate} time={createdTime}>
          <View>
            <Text style={styles.info}>Коментарий к вызову:</Text>
            <Text style={styles.info}>{description}</Text>
            <View style={styles.wrap}>
              <Button
                style={styles.mt}
                onPress={() => navigation()}
                icon={MapMarkerIcon}
              >
                Посмотреть карту
              </Button>
              <Button
                icon={CloseIcon}
              >
                Отменить вызов
              </Button>
            </View>
          </View>
        </CardLayout>
        <View>
          <Button mode="outlined" raised>Позвонить заказчику</Button>
          <Button mode="contained" style={styles.mt} onPress={() => onArrival()}>
            Бригада прибыла на вызов
          </Button>
        </View>
      </Layout>
    </ScrollView>
  )
}

const styles = StyleSheet.create({
  root: {
    backgroundColor: COLORS.white, flex: 1
  },
  info: {
    marginTop: 16,
    ...FONTS.text
  },
  wrap: {
    alignItems: 'flex-start',
    marginLeft: -10,
    marginVertical: 10,
    display: 'flex'
  },
  mt: {
    marginTop: 16
  }
})
