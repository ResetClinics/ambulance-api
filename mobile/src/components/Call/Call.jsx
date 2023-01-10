import {
  ScrollView, StyleSheet, Text, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React from 'react'
import { Layout } from '../Layout'
import { CardLayout } from '../CardLayout'
import { COLORS, FONTS } from '../../../constants'
import { MapMarkerIcon } from '../MapMarkerIcon'
import { CloseIcon } from '../CloseIcon'

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
const address = 'Пресненская наб., 2 (этаж 1)'
const subject = 'Вызов врача-нарколога'
const date = '12.12.2022'
const time = '12:45'

export const Call = ({ navigation, onArrival }) => (
  <ScrollView style={{ backgroundColor: COLORS.white }} showsVerticalScrollIndicator={false}>
    <Layout>
      <CardLayout address={address} subject={subject} date={date} time={time}>
        <View>
          <Text style={styles.info}>Коментарий к вызову:</Text>
          <Text style={styles.info}>{comment}</Text>
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

const styles = StyleSheet.create({
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
