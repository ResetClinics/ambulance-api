import React, { useState } from 'react'
import {
  Button, Card, Paragraph, Title
} from 'react-native-paper'
import {
  Image, StyleSheet, Text, View
} from 'react-native'
import { COLORS, FONTS } from '../../../constants'

export const CardItem = ({
  address, subject, date, time, comment, goToMapPage, status, children, text
}) => {
  const [active, setActive] = useState(false)

  const onDetailedClick = () => {
    setActive(!active)
  }

  const BtnChange = () => {
    if (active) {
      return <Button style={styles.btn} onPress={onDetailedClick} buttonColor={COLORS.white}>{text}</Button>
    }
    return <Button onPress={onDetailedClick} buttonColor={COLORS.white}>Подробнее</Button>

  }

  return (
    <Card style={styles.root} children>
      <Card.Content>
        {
          status && (<Text style={styles.status}>{status}</Text>)
        }
        <Title style={styles.title}>{address}</Title>
        <Paragraph style={styles.subtitle}>{subject}</Paragraph>
        <View style={styles.date}>
          <Text style={styles.text}>
            Дата:
            {' '}
            {date}
            {' '}
            г.
          </Text>
          <Text style={styles.text}>
            Время заказа:
            {' '}
            {time}
          </Text>
        </View>
        <View style={active ? styles.active : styles.content}>
          <Text style={styles.info}>Коментарий к вызову:</Text>
          <Text style={styles.info}>{comment}</Text>
          <View style={styles.wrap}>
            <Button
              onPress={() => goToMapPage()}
              style={styles.btn}
              icon={() => (
                <Image
                  source={require('../../../assets/images/map_marker.webp')}
                  style={{ width: 17, height: 23 }}
                />
              )}
            >
              Посмотреть карту
            </Button>
            <Button icon={() => (
              <Image
                source={require('../../../assets/images/close.webp')}
                style={{ width: 25, height: 24 }}
              />
            )}
            >
              Отменить вызов
            </Button>
          </View>
        </View>
      </Card.Content>
      <Card.Actions style={styles.actions}>
        {BtnChange()}
        {children}
      </Card.Actions>
    </Card>
  )
}

const styles = StyleSheet.create({
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 32
  },
  title: {
    ...FONTS.title,
    marginBottom: 16
  },
  subtitle: {
    ...FONTS.text,
    marginBottom: 16
  },
  date: {
    flexDirection: 'row',
    marginBottom: 8
  },
  status: {
    ...FONTS.text,
    color: COLORS.gray,
    marginBottom: 16
  },
  text: {
    width: '45%',
    ...FONTS.smallText
  },
  actions: {
    marginBottom: -27,
    right: 5
  },
  info: {
    marginTop: 16,
    ...FONTS.text
  },
  wrap: {
    alignItems: 'flex-start',
    marginLeft: -10,
    marginTop: 24,
    marginBottom: 10
  },
  btn: {
    marginBottom: 5
  },
  content: {
    display: 'none'
  },
  active: {
    display: 'flex'
  }
})
