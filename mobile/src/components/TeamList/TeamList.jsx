import {
  FlatList, Image, RefreshControl, StyleSheet, View
} from 'react-native'
import React, { useState } from 'react'
import {
  Card, Title, Paragraph, InternalTheme
} from 'react-native-paper'
import { COLORS, FONTS } from '../../../constants'
import labelImg from '../../../assets/images/label.png'

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
  const { name, speciality } = item
  return (
    <View style={styles.root}>
      <Card mode="outlined" theme={theme} style={styles.card}>
        <Card.Content style={styles.wrap}>
          <Title style={styles.title}>{name}</Title>
          <Paragraph style={styles.subtitle}>{speciality}</Paragraph>
        </Card.Content>

      </Card>
      <Image
        source={labelImg}
        style={styles.label}
      />
    </View>
  )
}

export const TeamList = () => {
  const [refreshing, setRefreshing] = useState(false)

  const onRefresh = () => {
    setRefreshing(true)
    setTimeout(() => {
      setRefreshing(false)
    }, 1500)
  }
  return (
    <View style={styles.layout}>
      <FlatList
        showsVerticalScrollIndicator={false}
        data={data}
        refreshControl={(
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor={COLORS.primary}
          />
        )}
        /* eslint-disable-next-line react/jsx-props-no-spreading */
        renderItem={({ item }) => <CardItem {...item} />}
      />
    </View>
  )
}

const styles = StyleSheet.create({
  layout: {
    flex: 1
  },
  root: {
    marginBottom: 32,
    position: 'relative',
  },
  card: {
    backgroundColor: COLORS.white,
  },
  label: {
    position: 'absolute',
    width: 46,
    height: 46,
    right: 10,
    bottom: -22
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

const theme = {
  ...InternalTheme,
  roundness: 2,
}
