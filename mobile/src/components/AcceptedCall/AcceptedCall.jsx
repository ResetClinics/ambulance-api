import {
  KeyboardAvoidingView, Platform,
  RefreshControl,
  ScrollView, StyleSheet, Text, TouchableOpacity, View
} from 'react-native'
import React, {useContext, useState} from 'react'
import { Button } from 'react-native-paper'
import { Form } from 'react-final-form'
import Modal from 'react-native-modal'
import { Masks } from 'react-native-mask-input'
import { CardLayout } from '../CardLayout'
import { COLORS, FONTS } from '../../../constants'
import { Layout } from '../Layout'
import { CloseIcon } from '../CloseIcon'
import { InputField } from '../InputField'
import { ModalWindow } from '../modal'
import {CurrentCallingContext} from "../../context/CurrentCallingContext";
import {formatDate, formatTime} from "../../helpers";

const MedicineItem = ({ name, count, openMedicineWindow }) => (
  <TouchableOpacity
    style={styles.item}
    onPress={openMedicineWindow}
    activeOpacity={1}
  >
    <Text style={styles.title}>{name}</Text>
    <Text style={styles.count}>{count}</Text>
  </TouchableOpacity>
)

export const AcceptedCall = ({ onAccepting }) => {

  const { currentCalling } = useContext(CurrentCallingContext)

  const {
    address, createdAt, description, name
  } = currentCalling

  const dateTime = new Date(createdAt)

  const createdDate = formatDate(dateTime)
  const createdTime = formatTime(dateTime)


  const [text, setText] = useState('')
  const [newNname, setName] = useState('')
  const [birthday, setBirthday] = useState('')
  const [refreshing, setRefreshing] = useState(false)
  const [isModalVisible, setModalVisible] = useState(false)
  const [medicine, setMedicine] = useState(false)

  const onSaveMedicine = (medicines) => {
    setMedicine(medicines)
  }

  const openMedicineWindow = () => {
    setModalVisible(true)
  }
  const closeMedicineWindow = () => {
    setModalVisible(false)
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
            colors={['#04607A']}
          />
                )}
      >
        <Layout>
          <CardLayout address={address} subject="" date={createdDate} time={createdTime}>
            <View>
              <Text style={styles.activeColor}>Коментарий к вызову:</Text>
              <Text style={styles.activeColor}>{description}</Text>
            </View>
            <View>
              <Text style={styles.info}>Данные заказчика:</Text>
              <Form
                onSubmit="onSubmit"
                render={() => (
                  <View>
                    <InputField name="fio" label="Фамилия Имя Отчество" value={name} onChangeText={(value) => setName(value)} />
                    <InputField
                      name="birthday"
                      label="Дата рождения"
                      value={birthday}
                      mask={Masks.DATE_DDMMYYYY}
                      onChangeText={(masked) => setBirthday(masked)}
                    />
                    <InputField name="document" label="Данные документа" value={text} onChangeText={(value) => setText(value)} />
                  </View>
                )}
              />
              {
                medicine
                && (
                  <View style={styles.holder}>
                    {
                      medicine.map((item) => (
                        <MedicineItem
                          {...item}
                          key={item.id}
                          openMedicineWindow={openMedicineWindow}
                        />
                      ))
                    }
                  </View>
                )
              }
              <View style={styles.wrapper}>
                <TouchableOpacity
                  onPress={openMedicineWindow}
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
          onBackdropPress={closeMedicineWindow}
          onSwipeComplete={closeMedicineWindow}
        >
          <ModalWindow closeMedicineWindow={closeMedicineWindow} onSaveMedicine={onSaveMedicine} />
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
  holder: {
    width: '100%',
    borderTopColor: COLORS.lightGray,
    borderBottomColor: COLORS.lightGray,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    backgroundColor: COLORS.thin,
    paddingVertical: 5,
    marginTop: 16,
    marginBottom: 8
  },
  item: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 12,
    paddingHorizontal: 8,
    width: '100%',
    alignItems: 'center'
  },
  title: {
    ...FONTS.chatText,
    letterSpacing: 0.5
  },
  count: {
    ...FONTS.count,
    marginLeft: 16,
    width: 36,
    textAlign: 'right'
  }
})
