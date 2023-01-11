import {
  FlatList, Image, StyleSheet, View
} from 'react-native'
import React from 'react'
import { Card, Title, Paragraph } from 'react-native-paper'
import { COLORS, FONTS } from '../../../constants'
import labelImg from '../../../assets/images/label.png'

const data = [
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },
]

const CardItem = (item) => {
  const { name, speciality } = item
  return (
    <Card style={styles.root}>
      <Card.Content style={styles.wrap}>
        <Title style={styles.title}>{name}</Title>
        <Paragraph style={styles.subtitle}>{speciality}</Paragraph>
      </Card.Content>
      <Image
        source={labelImg}
        style={styles.label}
      />
    </Card>
  )
}

export const TeamList = () => (
  <View style={styles.layout}>
    <FlatList
      showsVerticalScrollIndicator={false}
      data={data}
      /* eslint-disable-next-line react/jsx-props-no-spreading */
      renderItem={({ item }) => <CardItem {...item} />}
    />
  </View>
)

const styles = StyleSheet.create({
  layout: {
    flex: 1
  },
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 32,
    position: 'relative',

  },
  label: {
    position: 'absolute',
    width: 46,
    height: 46,
    right: 10,
    bottom: -23
  },
  title: {
    ...FONTS.title,
  },
  subtitle: {
    ...FONTS.text,
    marginTop: 16,
  },
  wrap: {
    padding: 16,
  }
})
