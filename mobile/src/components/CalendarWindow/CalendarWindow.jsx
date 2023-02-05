import React from 'react'
import {
  Image, StyleSheet, TouchableOpacity, View, Text
} from 'react-native'
import { LocaleConfig } from 'react-native-calendars'
import { Button } from 'react-native-paper'
import closeImg from '../../../assets/images/close.png'
import {COLORS, FONTS} from '../../../constants'
import { DateRangePicker } from '../DateRangePicker'

LocaleConfig.locales.ru = {
  monthNames: [
    'Январь',
    'Февраль',
    'Март',
    'Апрель',
    'Май',
    'Июнь',
    'Июль',
    'Август',
    'Сентябрь',
    'Октябрь',
    'Нояврь',
    'Декабрь'
  ],
  monthNamesShort: ['Янв.', 'Фев.', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль.', 'Авг.', 'Сент.', 'Окт.', 'Ноябрь', 'Дек.'],
  dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
  dayNamesShort: ['Вс.', 'Пн.', 'Вт.', 'Ср.', 'Чт.', 'Пт.', 'Сб.'],
  today: 'Сегодня'
}
LocaleConfig.defaultLocale = 'ru'

export const CalendarWindow = ({ toggleModal }) => (
  <View style={styles.root}>
    <TouchableOpacity
      onPress={toggleModal}
      activeOpacity={1}
    >
      <Image
        source={closeImg}
        style={styles.img}
        resizeMode="contain"
      />
    </TouchableOpacity>
    <View style={styles.container}>
      <View style={styles.head}>
        <Text style={styles.heading}>Январь 2023</Text>
      </View>
      <DateRangePicker
        initialRange={['2022-04-01', '2022-04-10']}
        theme={theme}
        onSuccess={() => null}
      />
    </View>
    <View style={styles.buttons}>
      <Button style={styles.btn} onPress={() => console.log('press')} mode="outlined">Сбросить</Button>
      <Button style={styles.btn} onPress={() => console.log('press')} mode="contained">Сохранить</Button>
    </View>
  </View>
)

const theme = {
  markColor: COLORS.blue,
  markTextColor: COLORS.white,
  todayTextColor: '#69d4e7',
  dayTextColor: '#222B45',
  textSectionTitleColor: '#8F9BB3',
  monthTextColor: '#49454F',
  textMonthFontSize: 16,
  textMonthFontFamily: 'Roboto-Medium',
  textDisabledColor: '#8F9BB3',
  textDayFontSize: 15,
  textDayHeaderFontSize: 13,
  textDayHeaderFontFamily: 'Roboto-Medium',
  'stylesheet.calendar.header': {
    header: {
      paddingHorizontal: 7,
      paddingVertical: 10,
    },
  },
}

const styles = StyleSheet.create({
  img: {
    width: 30,
    height: 30,
    marginLeft: 'auto',
    marginBottom: 15,
    marginRight: 16
  },
  container: {
    backgroundColor: COLORS.white,
    maxHeight: '82%',
    marginTop: 'auto',
    borderTopRightRadius: 6,
    borderTopLeftRadius: 6,
    overflow: 'hidden',
    marginBottom: 0,
    paddingTop: 70,
    position: 'relative'
  },
  head: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    alignItems: 'center',
    paddingTop: 24,
    paddingBottom: 28,
    paddingHorizontal: 28,
    width: '100%',
    borderBottomColor: COLORS.darkGrey,
    borderBottomWidth: 1,
    backgroundColor: COLORS.white,
    zIndex: 12
  },
  heading: {
    ...FONTS.heading
  },
  buttons: {
    flexDirection: 'row',
    paddingTop: 4,
    paddingBottom: 16,
    paddingHorizontal: 16,
    justifyContent: 'space-between',
    backgroundColor: COLORS.white
  },
  btn: {
    width: '47%'
  }
})
