import { FlatList, View } from "react-native";
import React from "react";
import { Card, Title, Paragraph } from 'react-native-paper';
import { COLORS } from "../../../../constants";
import SVGImg from '../../../../assets/label.svg';

const data = [
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },{
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },{
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },
]

const CardItem = (item) => {
  return (
    <View style={styles.wrapper}>
      <Card style={styles.root}>
        <Card.Content>
          <Title>{item.name}</Title>
          <Paragraph>{item.speciality}</Paragraph>
        </Card.Content>
      </Card>
      <SVGImg style={styles.label} width={46} height={46} />
    </View>
  )
}

export const TeamList = () => {
  return (
    <View style={styles.layout}>
      <FlatList
        data={data}
        renderItem={({item}) => <CardItem {...item} />}
      />
    </View>
  )
}

const styles = {
  layout: {
    flex: 1
  },
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 32
  },
  wrapper: {
    position: 'relative',
  },
  label: {
    position: 'absolute',
    borderRadius: 100,
    backgroundColor: COLORS.white,
    right: 10,
    bottom: 10
  }
}
