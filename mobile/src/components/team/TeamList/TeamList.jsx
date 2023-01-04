import { FlatList, StyleSheet, View } from 'react-native'
import React from 'react'
import { Card, Title, Paragraph } from 'react-native-paper'
import { COLORS, FONTS } from '../../../../constants'
import SVGImg from '../../../../assets/images/label.svg'

const data = [
  {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  }, {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  }, {
    name: 'Агибалова Татьяна Васильевна',
    speciality: 'Психиатор- нарколог'
  },
  {
    name: 'Иван Иванович Иванов',
    speciality: 'Администратор'
  },
]

const CardItem = (item) => (
  <View style={styles.wrapper}>
    <Card style={styles.root}>
      <Card.Content>
        <Title style={styles.title}>{item.name}</Title>
        <Paragraph style={styles.subtitle}>{item.speciality}</Paragraph>
      </Card.Content>
    </Card>
    <SVGImg style={styles.label} width={46} height={46} />
  </View>
)

export const TeamList = () => (
  <View style={styles.layout}>
    <FlatList
      data={data}
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
    marginBottom: 32
  },
  wrapper: {
    position: 'relative',
  },
  title: {
    ...FONTS.title,
    marginBottom: 16
  },
  subtitle: {
    ...FONTS.text,
  },
  label: {
    position: 'absolute',
    borderRadius: 100,
    backgroundColor: COLORS.white,
    right: 10,
    bottom: 10
  }
})
