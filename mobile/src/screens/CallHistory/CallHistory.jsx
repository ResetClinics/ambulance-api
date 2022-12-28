import React from "react";
import { Layout } from "../../shared";
import { FlatList } from "react-native";
import { CardItem } from "../../components";
import { data } from "../data/data";

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
        renderItem={({item}) => <CardItem {...item} goToMapPage={goToMapPage} text='Свернуть'/>}
      />
    </Layout>
  )
}
