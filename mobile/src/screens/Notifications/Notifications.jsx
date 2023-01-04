import React from "react";
import { Button } from 'react-native-paper';
import { Layout } from "../../shared";
import { FlatList } from "react-native";
import { CardItem } from "../../components";
import { data } from "../../data/data";

export const Notifications = ({navigation}) => {
  const onAccepting = () => {
    navigation.navigate('Текущий вызов')
  }

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
        renderItem={({item}) => <CardItem {...item} status='' onAccepting={onAccepting} goToMapPage={goToMapPage} text="Позвонить заказчику">{<Button onPress={() => onAccepting()}>Принять</Button>}</CardItem>}
      />
    </Layout>
  )
}
