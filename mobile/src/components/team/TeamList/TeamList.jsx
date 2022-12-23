import { View } from "react-native";
import React from "react";
import { Card, Title, Paragraph } from 'react-native-paper';
import { COLORS } from "../../../../constants";

const data = [
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
]

const CardItem = (item) => {
  return (
    <Card style={styles.root}>
      <Card.Content>
        <Title>{item.name}</Title>
        <Paragraph>{item.speciality}</Paragraph>
      </Card.Content>
    </Card>
  )
}

export const TeamList = () => {
  return (
    <View>
      {
        data.map((item, key) => <CardItem {...item} key={key} />)
      }
    </View>
  )
}

const styles = {
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 32
  }
}
