import { Image, ScrollView, StyleSheet, Text, View } from "react-native";
import { Layout } from "../Layout";
import { CardLayout } from "../CardLayout";
import { Button } from "react-native-paper";
import React from "react";
import { COLORS, FONTS } from "../../../constants";

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
  address = 'Пресненская наб., 2 (этаж 1)',
  subject = 'Вызов врача-нарколога',
  date = '12.12.2022',
  time = '12:45';

export const Call = ({ navigation, onArrival } ) => {
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
                    source={require('../../../assets/images/map_marker.webp')}
                    style={{width: 17, height: 23}}
                  />
                )}>Посмотреть карту</Button>
              <Button icon={() => (
                <Image
                  source={require('../../../assets/images/close.webp')}
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
