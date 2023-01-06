import {
  Image, ScrollView, StyleSheet, Text, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React from 'react'
import { Layout } from '../Layout'
import { CardLayout } from '../CardLayout'
import { COLORS, FONTS } from '../../../constants'
import closeImg from '../../../assets/images/close.webp'
import markerImg from '../../../assets/images/map_marker.webp'

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
const address = 'Пресненская наб., 2 (этаж 1)'
const subject = 'Вызов врача-нарколога'
const date = '12.12.2022'
const time = '12:45'

export const Call = ({ navigation, onArrival }) => (
  <ScrollView style={{ backgroundColor: COLORS.white }}>
    <Layout>
      <CardLayout address={address} subject={subject} date={date} time={time}>
        <View>
          <Text style={styles.info}>Коментарий к вызову:</Text>
          <Text style={styles.info}>{comment}</Text>
          <View style={styles.wrap}>
            <Button
              style={styles.mt}
              onPress={() => navigation()}
              /* eslint-disable-next-line react/no-unstable-nested-components */
              icon={() => (
                <Image
                  source={markerImg}
                  style={styles.smallImg}
                />
              )}
            >
              Посмотреть карту
            </Button>
            <Button
              /* eslint-disable-next-line react/no-unstable-nested-components */
              icon={() => (
                <Image
                  source={closeImg}
                  style={styles.img}
                />
              )}
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
  img: {
    width: 25, height: 24
  },
  smallImg: {
    width: 17, height: 23
  },
  mt: {
    marginTop: 16
  }
})
