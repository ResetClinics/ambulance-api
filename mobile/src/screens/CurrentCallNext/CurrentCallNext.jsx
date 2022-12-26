import { Image, ScrollView, Text, View } from "react-native";
import React, { useState } from "react";
import { CardLayout } from "../../components";
import { Layout } from "../../shared";
import { Button, TextInput } from "react-native-paper";
import { COLORS, SIZES } from "../../../constants";
import { createNativeStackNavigator } from "@react-navigation/native-stack";

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
  address = 'Пресненская наб., 2 (этаж 1)',
  subject = 'Вызов врача-нарколога',
  date = '12.12.2022',
  time = '12:45';

const Call = ({ navigation }) => {
  const [active, setActive] = useState(false);
  const [text, setText] = React.useState("");

  const onAccepting = () => {
    navigation.navigate('Уведомления')
  }

  const goToMapPage = () => {
    navigation.navigate('Маршрут')
  }
  const onDetailedClick = () => {
    setActive(!active)
  }
  return (
    <ScrollView style={styles.root}>
      <Layout>
        <CardLayout address={address} subject={subject} date={date} time={time}>
          <View>
            <Text style={active ? styles.activeColor : styles.info}>Коментарий к вызову:</Text>
            <Text style={active ? styles.activeColor : styles.info}>{comment}</Text>
            <View style={active ? styles.hide : styles.wrap}>
              <Button
                style={styles.btn}
                onPress={() => goToMapPage()}
                icon={() => (
                  <Image
                    source={require('../../../assets/map_marker.png')}
                    style={{ width: 17, height: 23 }}
                  />
                )}>Посмотреть карту</Button>
              <Button icon={() => (
                <Image
                  source={require('../../../assets/close.png')}
                  style={{ width: 24, height: 24 }}
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
                onChangeText={text => setText(text)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Дата рождения"
                value={text}
                onChangeText={text => setText(text)}
              />
              <TextInput
                style={styles.input}
                mode="outlined"
                focused
                label="Данные документа"
                value={text}
                onChangeText={text => setText(text)}
              />
            </View>
            <View style={styles.wrapper}>
              <Button
                style={styles.btn}
                icon={() => (
                  <Image
                    source={require('../../../assets/close.png')}
                    style={{ width: 24, height: 24 }}
                  />
                )}>Добавить услуги</Button>
              <Button icon={() => (
                <Image
                  source={require('../../../assets/close.png')}
                  style={{ width: 24, height: 24 }}
                />
              )}>Добавить список медикаментов</Button>
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

const Map = () => {
  return <Layout>
    <View style={styles.btnHolder}>
      <Button mode="text">Map is here</Button>
    </View>
  </Layout>
}


const Stack = createNativeStackNavigator()
export const CurrentCallNext = ({navigation}) => {
  return (
    <Stack.Navigator>
      <Stack.Screen name="Home" component={Call} options={{title: ''}} />
      <Stack.Screen name="Маршрут" component={Map} options={{title: 'Маршрут'}} />
    </Stack.Navigator>
  )
}

const styles = {
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
  }
}

