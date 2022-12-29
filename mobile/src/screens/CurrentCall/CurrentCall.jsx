import { Image, ScrollView, Text, TouchableOpacity, View } from "react-native";
import React, { useState } from "react";
import {CardLayout, ModalWindow} from "../../components";
import { Layout } from "../../shared";
import { COLORS, SIZES } from "../../../constants";
import { Appbar } from 'react-native-paper';
import { Button, TextInput } from 'react-native-paper';
import { MagicModalPortal, magicModal } from 'react-native-magic-modal';

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
  address = 'Пресненская наб., 2 (этаж 1)',
  subject = 'Вызов врача-нарколога',
  date = '12.12.2022',
  time = '12:45';

export const CurrentCall = ({navigation}) => {
  const [active, setActive] = useState(false);
  const [text, setText] = React.useState("");

  const onAccepting = () => {
    navigation.navigate('Уведомления')
  }

  const onDetailedClick = () => {
    setActive(!active)
  }

  const STATUSES = {
    ASSIGNED: 'assigned',
    ROUTE: 'route'
  }
  const [status, setStatus] = useState(STATUSES.ASSIGNED);
  switch (status) {
    case STATUSES.ROUTE:
      return <Layout>
        <Appbar.Header>
          <Appbar.BackAction onPress={() => setStatus(STATUSES.ASSIGNED)} />
          <Appbar.Content title="Маршрут до места вызова" style={styles.title}/>
        </Appbar.Header>
        {/*<View style={styles.btnHolder}>
          <Button mode="text" onPress={() => setStatus(STATUSES.ASSIGNED)}>Маршрут до места вызова</Button>
        </View>*/}
      </Layout>
    default:
      return (
        <ScrollView style={styles.root}>
          <Layout>
            <MagicModalPortal />
            <CardLayout address={address} subject={subject} date={date} time={time}>
              <View>
                <Text style={active ? styles.activeColor : styles.info}>Коментарий к вызову:</Text>
                <Text style={active ? styles.activeColor : styles.info}>{comment}</Text>
                <View style={active ? styles.hide : styles.wrap}>
                  <Button
                    style={styles.btn}
                    onPress={() => setStatus(STATUSES.ROUTE)}
                    icon={() => (
                      <Image
                        source={require('../../../assets/images/map_marker.webp')}
                        style={{ width: 17, height: 23 }}
                      />
                    )}>Посмотреть карту</Button>
                  <Button icon={() => (
                    <Image
                      source={require('../../../assets/images/close.webp')}
                      style={{ width: 25, height: 24 }}
                    />
                  )}>Отменить вызов</Button>
                </View>
              </View>
              <View style={active ? styles.show : styles.hide}>
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
                    onPress={() => magicModal.show(() => <ModalWindow label="Поиск услуги" />)}
                  >
                    <Button
                      style={styles.btn}
                      icon={() => (
                        <Image
                          source={require('../../../assets/images/close.webp')}
                          style={{ width: 25, height: 24 }}
                        />
                      )}>Добавить услуги</Button>
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
                      )}>Добавить список медикаментов</Button>
                  </TouchableOpacity>
                </View>
                <Button mode="contained" raised style={styles.gray} textColor={COLORS.gray}>Стоимость оказаных услуг</Button>
              </View>
            </CardLayout>
            <View style={active ? styles.hide : styles.show}>
              <Button mode="outlined" raised>Позвонить заказчику</Button>
              <Button mode="contained" style={styles.btn} onPress={() => onDetailedClick()}>Бригада прибыла на вызов</Button>
            </View>
            <View style={active ? styles.show : styles.hide}>
              <Button  mode="contained" onPress={() => onAccepting()}>Вызов завершен</Button>
              <Button mode="contained" style={styles.btn}>Повтор процедуры</Button>
              <Button mode="contained" style={styles.btn}>Кодирование</Button>
              <Button mode="contained" style={styles.btn} onPress={() => onAccepting()}>Госпитализация</Button>
            </View>
          </Layout>
        </ScrollView>
      )
  }
}

const styles = {
  title: {
    color: COLORS.primary,
    fontSize: 12
  },
  root: {
    flex: 1,
    backgroundColor: COLORS.white
  },
  info: {
    fontSize: SIZES.fs16,
    color: COLORS.black,
    letterSpacing: 0.4,
    lineHeight: 16,
    marginTop: 16
  },
  activeColor: {
    fontSize: SIZES.fs16,
    color: COLORS.gray,
    letterSpacing: 0.4,
    lineHeight: 16,
    marginTop: 16
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
  gray: {
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
}
