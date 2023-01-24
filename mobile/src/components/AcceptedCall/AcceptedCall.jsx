import {
  KeyboardAvoidingView, Platform,
  RefreshControl,
  ScrollView, StyleSheet, Text, TouchableOpacity, View
} from 'react-native'
import React, { useState } from 'react'
import { Button } from 'react-native-paper'
import { Form } from 'react-final-form'
import Modal from 'react-native-modal'
import { CardLayout } from '../CardLayout'
import { COLORS, FONTS } from '../../../constants'
import { Layout } from '../Layout'
import { CloseIcon } from '../CloseIcon'
import { InputField } from '../InputField'
import { ModalWindow } from '../modal'

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
const address = 'Пресненская наб., 2 (этаж 1)'
const subject = 'Вызов врача-нарколога'
const date = '12.12.2022'
const time = '12:45'

export const AcceptedCall = ({ onAccepting }) => {
  const [text, setText] = useState('')
  const [name, setName] = useState('')
  const [birthday, setBirthday] = useState('')
  const [refreshing, setRefreshing] = useState(false)
  const [isModalVisible, setModalVisible] = useState(false)

  const toggleModal = () => {
    setModalVisible(!isModalVisible)
  }

  const onRefresh = () => {
    setRefreshing(true)
    setTimeout(() => {
      setRefreshing(false)
    }, 1500)
  }

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.root}>
      <ScrollView
        style={styles.root}
        showsVerticalScrollIndicator={false}
        refreshControl={(
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor={COLORS.primary}
          />
                )}
      >
        <Layout>
          <CardLayout address={address} subject={subject} date={date} time={time}>
            <View>
              <Text style={styles.activeColor}>Коментарий к вызову:</Text>
              <Text style={styles.activeColor}>{comment}</Text>
            </View>
            <View>
              <Text style={styles.info}>Данные заказчика:</Text>
              <Form
                onSubmit="onSubmit"
                render={() => (
                  <View>
                    <InputField name="fio" label="Фамилия Имя Отчество" value={name} onChangeText={(value) => setName(value)} />
                    <InputField name="birthday" label="Дата рождения" value={birthday} onChangeText={(value) => setBirthday(value)} />
                    <InputField name="document" label="Данные документа" value={text} onChangeText={(value) => setText(value)} />
                  </View>
                )}
              />
              <View style={styles.wrapper}>
                <TouchableOpacity
                  onPress={toggleModal}
                >
                  <Button
                    style={styles.mt}
                    icon={CloseIcon}
                  >
                    Добавить медикаменты
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
        <Modal
          isVisible={isModalVisible}
          animationInTiming={600}
          animationOutTiming={600}
          backdropTransitionOutTiming={700}
          avoidKeyboard
          backdropColor={COLORS.primary}
          backdropOpacity={0.4}
          onBackdropPress={toggleModal}
          onSwipeComplete={toggleModal}
        >
          <ModalWindow toggleModal={toggleModal} />
        </Modal>
      </ScrollView>
    </KeyboardAvoidingView>
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
