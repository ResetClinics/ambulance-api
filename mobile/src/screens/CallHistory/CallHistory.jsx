import React from "react";
import { Layout } from "../../shared";
import { FlatList } from "react-native";
import { CardItem } from "../../components";

const data = [
  {
    address: 'Пресненская наб., 2 (этаж 1)',
    subject: 'Вызов врача-нарколога',
    date: '12.12.2022',
    speciality: 'Психиатор- нарколог',
    time: '12:45',
    comment: 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
    status: 'Активный вызов'
  },
  {
    address: 'Пресненская наб., 2 (этаж 1)',
    subject: 'Вызов врача-нарколога',
    date: '12.12.2022',
    time: '12:45',
    comment: 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь, нужна детоксикация' +
      ' организма , возмоно психотерапевтическая помощь',
    status: 'Вызов завершен'
  },
  {
    address: 'Пресненская набережная, 2 (этаж 1), кв. 589',
    subject: 'Вызов врача-нарколога',
    date: '12.12.2022',
    time: '12:45',
    comment: 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь, нужна детоксикация' +
      ' организма , возмоно психотерапевтическая помощь',
    status: 'Вызов завершен'
  },
]

export const CallHistory = ({navigation}) => {
  const goToMapPage = () => {
    navigation.navigate('Маршрут', {
      screen: 'Home',
      params: {
        screen: 'Маршрут',
      },
    });
  }
  return (
    <Layout>
      <FlatList
        data={data}
        renderItem={({item}) => <CardItem {...item} goToMapPage={goToMapPage} />}
      />
    </Layout>
  )
}
