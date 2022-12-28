import React, { useState } from "react";
import { Button, Card, Paragraph, Title } from "react-native-paper";
import { Image, Text, View } from "react-native";
import { COLORS, SIZES } from "../../../constants";

export  const CardItem = ({ address, subject, date, time, comment, goToMapPage, status, children, text } ) => {
  const [active, setActive] = useState(false)

  const onDetailedClick = () => {
    setActive(!active)
  }

  const BtnChange = () => {
    if(active) {
      return <Button style={styles.btn} onPress={onDetailedClick} buttonColor={COLORS.white}>{text}</Button>
    } else {
      return <Button onPress={onDetailedClick} buttonColor={COLORS.white}>Подробнее</Button>
    }
  }

  return (
    <Card style={styles.root} children>
      <Card.Content>
        {
          status && (<Text style={styles.status}>{status}</Text>)
        }
        <Title>{address}</Title>
        <Paragraph>{subject}</Paragraph>
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
              style={styles.btn} icon={() => (
              <Image
                source={require('../../../assets/map_marker.png')}
                style={{ width: 17, height: 23 }}
              />
            )}>Посмотреть карту</Button>
            <Button icon={() => (
              <Image
                source={require('../../../assets/close.png')}
                style={{ width: 24, height: 24 }}
              />
            )}>Отменить вызов</Button>
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

const styles = {
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 32
  },
  date: {
    flexDirection: 'row',
    marginTop: 16,
    marginBottom: 8
  },
  status: {
    letterSpacing: 0.4,
    lineHeight: 16,
    fontSize: SIZES.fs16,
    color: COLORS.gray,
  },
  text: {
    color: COLORS.gray,
    fontSize: SIZES.fs12,
    letterSpacing: 0.4,
    lineHeight: 16,
    width: '45%',
  },
  actions: {
    marginBottom: -27,
    right: 5
  },
  info: {
    fontSize: SIZES.fs16,
    color: COLORS.black,
    letterSpacing: 0.4,
    lineHeight: 16,
    marginTop: 16
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
}
