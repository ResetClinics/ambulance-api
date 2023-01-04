import {
  Image, ScrollView, StyleSheet, Text, TouchableOpacity, View
} from 'react-native'
import React, { useState } from 'react'
import { magicModal, MagicModalPortal } from 'react-native-magic-modal'
import { Button, TextInput } from 'react-native-paper'
import { CardLayout } from '../CardLayout'
import { ModalWindow } from '../modal'
import { COLORS, FONTS } from '../../../constants'
import { Layout } from '../Layout'

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
const address = 'Пресненская наб., 2 (этаж 1)'
const subject = 'Вызов врача-нарколога'
const date = '12.12.2022'
const time = '12:45'

export const AcceptedCall = ({ onAccepting }) => {
  const [text, setText] = useState('')

  return (
    <ScrollView style={{ backgroundColor: COLORS.white, flex: 1 }}>
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
                style={styles.input}
                mode="outlined"
                focused
                label="Фамилия Имя Отчество"
                value={text}
                onChangeText={(textName) => setText(textName)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Дата рождения"
                value={text}
                onChangeText={(textDate) => setText(textDate)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Данные документа"
                value={text}
                onChangeText={(textInfo) => setText(textInfo)}
              />
            </View>
            <View style={styles.wrapper}>
              <TouchableOpacity
                onPress={() => magicModal.show(() => <ModalWindow label="Поиск услуги" />)}
              >
                <Button
                  style={{ marginTop: 16 }}
                  icon={() => (
                    <Image
                      source={require('../../../assets/images/close.webp')}
                      style={{ width: 25, height: 24 }}
                    />
                  )}
                >
                  Добавить услуги
                </Button>
              </TouchableOpacity>
              <TouchableOpacity
                onPress={() => magicModal.show(() => <ModalWindow label="Поиск медикаментов" />)}
              >
                <Button
                  icon={() => (
                    <Image
                      source={require('../../../assets/images/close.webp')}
                      style={{ width: 25, height: 24 }}
                    />
                  )}
                >
                  Добавить список медикаментов
                </Button>
              </TouchableOpacity>
            </View>
            <Button mode="contained" raised style={styles.costBtn} textColor={COLORS.gray}>
              Стоимость оказаных
              услуг
            </Button>
          </View>
        </CardLayout>
        <View>
          <Button mode="contained" onPress={() => onAccepting()}>Вызов завершен</Button>
          <Button mode="contained" style={{ marginTop: 16 }}>Повтор процедуры</Button>
          <Button mode="contained" style={{ marginTop: 16 }}>Кодирование</Button>
          <Button mode="contained" style={{ marginTop: 16 }} onPress={() => onAccepting()}>Госпитализация</Button>
        </View>
      </Layout>
    </ScrollView>
  )
}

const styles = StyleSheet.create({
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
  costBtn: {
    backgroundColor: '#f1f1f199',
    borderWidth: 1,
    borderColor: '#0000001f',
    borderRadius: 4,
    alignItems: 'flex-start',
  },
})
