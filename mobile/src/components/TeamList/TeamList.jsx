import { FlatList, StyleSheet, View } from 'react-native'
import React from 'react'
import { Card, Title, Paragraph } from 'react-native-paper'
import { COLORS, FONTS } from '../../../constants'

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
    <View style={styles.wrapper}>
      <Card style={styles.root}>
        <Card.Content>
          <Title style={styles.title}>{name}</Title>
          <Paragraph style={styles.subtitle}>{speciality}</Paragraph>
        </Card.Content>
      </Card>
    </View>
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
    marginBottom: 32
  },
  wrapper: {
    position: 'relative',
  },
  title: {
    ...FONTS.title,
  },
  subtitle: {
    ...FONTS.text,
    marginTop: 16
  },
})
