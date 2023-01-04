import React, { useState } from "react";
import { Button, TextInput } from "react-native-paper";
import { Image, ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { COLORS, FONTS } from "../../../../constants";
import { magicModal, MagicModalPortal } from "react-native-magic-modal";
import { CardLayout, Layout, ModalWindow } from "../../../components";

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
  address = 'Пресненская наб., 2 (этаж 1)',
  subject = 'Вызов врача-нарколога',
  date = '12.12.2022',
  time = '12:45';

const Call = ({ navigation, onArrival } ) => {
  return (
    <ScrollView style={styles.root}>
      <Layout>
        <CardLayout address={address} subject={subject} date={date} time={time}>
          <View>
            <Text style={styles.info}>Коментарий к вызову:</Text>
            <Text style={styles.info}>{comment}</Text>
            <View style={styles.wrap}>
              <Button
                style={styles.btn}
                onPress={() => navigation()}
                icon={() => (
                  <Image
                    source={require('../../../../assets/images/map_marker.webp')}
                    style={{width: 17, height: 23}}
                  />
                )}>Посмотреть карту</Button>
              <Button icon={() => (
                <Image
                  source={require('../../../../assets/images/close.webp')}
                  style={{width: 25, height: 24}}
                />
              )}>Отменить вызов</Button>
            </View>
          </View>
        </CardLayout>
        <View>
          <Button mode="outlined" raised>Позвонить заказчику</Button>
          <Button mode="contained" style={styles.btn} onPress={() => onArrival()}>Бригада прибыла на
            вызов</Button>
        </View>
      </Layout>
    </ScrollView>
  )
}

const AcceptedCall = ({ onAccepting }) => {
  const [text, setText] = useState("");

  return (
    <ScrollView style={styles.root}>
      <Layout>
        <MagicModalPortal/>
        <CardLayout address={address} subject={subject} date={date} time={time}>
          <View>
            <Text style={styles.activeColor}>Коментарий к вызову:</Text>
            <Text style={styles.activeColor}>{comment}</Text>
          </View>
          <View>
            <Text style={styles.info}>Данные заказчика:</Text>
            <View style={styles.inputsHolder}>
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Фамилия Имя Отчество"
                value={text}
                onChangeText={textName => setText(textName)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Дата рождения"
                value={text}
                onChangeText={textDate => setText(textDate)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Данные документа"
                value={text}
                onChangeText={textInfo => setText(textInfo)}
              />
            </View>
            <View style={styles.wrapper}>
              <TouchableOpacity
                onPress={() => magicModal.show(() => <ModalWindow label="Поиск услуги"/>)}
              >
                <Button
                  style={styles.btn}
                  icon={() => (
                    <Image
                      source={require('../../../../assets/images/close.webp')}
                      style={{width: 25, height: 24}}
                    />
                  )}>Добавить услуги</Button>
              </TouchableOpacity>
              <TouchableOpacity
                onPress={() => magicModal.show(() => <ModalWindow label="Поиск медикаментов"/>)}
              >
                <Button
                  icon={() => (
                    <Image
                      source={require('../../../../assets/images/close.webp')}
                      style={{width: 25, height: 24}}
                    />
                  )}>Добавить список медикаментов</Button>
              </TouchableOpacity>
            </View>
            <Button mode="contained" raised style={styles.costBtn} textColor={COLORS.gray}>Стоимость оказаных
              услуг</Button>
          </View>
        </CardLayout>
        <View>
          <Button mode="contained" onPress={() => onAccepting()}>Вызов завершен</Button>
          <Button mode="contained" style={styles.btn}>Повтор процедуры</Button>
          <Button mode="contained" style={styles.btn}>Кодирование</Button>
          <Button mode="contained" style={styles.btn} onPress={() => onAccepting()}>Госпитализация</Button>
        </View>
      </Layout>
    </ScrollView>
  )
}

export  const CurrentCallPage = ({ navigation } ) => {
  const [arrival, setArrival] = useState(false);
  const goToMapPage = () => {
    navigation.navigate('Маршрут')
  }
  const onArrival = () => {
    setArrival(true)
  }

  const onAccepting = () => {
    navigation.navigate('Уведомления')
  }

  if(arrival) {
    return <AcceptedCall onAccepting={onAccepting} />
  }
  return  <Call navigation={goToMapPage} onArrival={onArrival} />
}

const styles = StyleSheet.create({
  title: {
    color: COLORS.primary,
    fontSize: 12
  },
  root: {
    flex: 1,
    backgroundColor: COLORS.white
  },
  info: {
    marginTop: 16,
    ...FONTS.text
  },
  activeColor: {
    marginTop: 16,
    ...FONTS.text,
    color: COLORS.gray,
  },
  wrap: {
    alignItems: 'flex-start',
    marginLeft: -10,
    marginVertical: 10,
    display: 'flex'
  },
  wrapper: {
    alignItems: 'flex-start',
    marginLeft: -10,
    marginTop: -10,
    marginBottom: 16,
  },
  hide: {
    display: 'none'
  },
  show: {
    display: 'flex',
  },
  btn: {
    marginTop: 16
  },
  costBtn: {
    backgroundColor: '#f1f1f199',
    borderWidth: 1,
    borderColor: '#0000001f',
    borderRadius: 4,
    alignItems: 'flex-start',
  },
  inputsHolder: {
    marginTop: 7
  },
  input: {
    marginBottom: 10,
  },
});
