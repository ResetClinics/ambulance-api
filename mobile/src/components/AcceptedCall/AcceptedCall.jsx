import {
  ScrollView, StyleSheet, Text, TouchableOpacity, View
} from 'react-native'
import React, { useState } from 'react'
import { magicModal, MagicModalPortal } from 'react-native-magic-modal'
import { Button, TextInput } from 'react-native-paper'
import { CardLayout } from '../CardLayout'
import { ModalWindow } from '../modal'
import { COLORS, FONTS } from '../../../constants'
import { Layout } from '../Layout'
import { CloseIcon } from '../CloseIcon'

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
const address = 'Пресненская наб., 2 (этаж 1)'
const subject = 'Вызов врача-нарколога'
const date = '12.12.2022'
const time = '12:45'

export const AcceptedCall = ({ onAccepting }) => {
  const [text, setText] = useState('')
  const [name, setName] = useState('')
  const [birthday, setBirthday] = useState('')

  return (
    <ScrollView style={styles.root} showsVerticalScrollIndicator={false}>
      <Layout>
        <MagicModalPortal />
        <CardLayout address={address} subject={subject} date={date} time={time}>
          <View>
            <Text style={styles.activeColor}>Коментарий к вызову:</Text>
            <Text style={styles.activeColor}>{comment}</Text>
          </View>
          <View>
            <Text style={styles.info}>Данные заказчика:</Text>
            <View>
              <TextInput
                style={styles.mt}
                mode="outlined"
                focused
                label="Фамилия Имя Отчество"
                value={name}
                onChangeText={(value) => setName(value)}
              />
              <TextInput
                style={styles.mt}
                mode="outlined"
                focused
                label="Дата рождения"
                value={birthday}
                onChangeText={(value) => setBirthday(value)}
              />
              <TextInput
                style={styles.mt}
                mode="outlined"
                focused
                label="Данные документа"
                value={text}
                onChangeText={(value) => setText(value)}
              />
            </View>
            <View style={styles.wrapper}>
              <TouchableOpacity
                onPress={() => magicModal.show(<ModalWindow label="Поиск услуги" />)}
              >
                <Button
                  style={styles.mt}
                  icon={CloseIcon}
                >
                  Добавить услуги
                </Button>
              </TouchableOpacity>
              <TouchableOpacity
                onPress={() => magicModal.show(<ModalWindow label="Поиск медикаментов" />)}
              >
                <Button
                  icon={CloseIcon}
                >
                  Добавить список медикаментов
                </Button>
              </TouchableOpacity>
            </View>
            <Button mode="contained" raised style={styles.cost} textColor={COLORS.gray}>Стоимость оказаных услуг</Button>
          </View>
        </CardLayout>
        <View>
          <Button mode="contained" onPress={() => onAccepting()}>Вызов завершен</Button>
          <Button mode="contained" style={styles.mt}>Повтор процедуры</Button>
          <Button mode="contained" style={styles.mt}>Кодирование</Button>
          <Button mode="contained" style={styles.mt} onPress={() => onAccepting()}>Госпитализация</Button>
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
    ...FONTS.text,
    marginBottom: 7
  },
  activeColor: {
    marginTop: 16,
    ...FONTS.text,
    color: COLORS.gray,
  },
  wrapper: {
    alignItems: 'flex-start',
    marginLeft: -10,
    marginTop: -10,
    marginBottom: 16,
  },
  cost: {
    backgroundColor: COLORS.light,
    borderWidth: 1,
    borderColor: COLORS.lightGray,
    borderRadius: 4,
    alignItems: 'flex-start',
  },
  mt: {
    marginTop: 16
  },
})
