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
    <ScrollView style={{ backgroundColor: COLORS.white}}>
      <Layout>
        <CardLayout address={address} subject={subject} date={date} time={time}>
          <View>
            <Text style={styles.info}>Коментарий к вызову:</Text>
            <Text style={styles.info}>{comment}</Text>
            <View style={styles.wrap}>
              <Button
                style={{marginTop: 16}}
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
          <Button mode="contained" style={{marginTop: 16}} onPress={() => onArrival()}>Бригада прибыла на
            вызов</Button>
        </View>
      </Layout>
    </ScrollView>
  )
}

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
});
