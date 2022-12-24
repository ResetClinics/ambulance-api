import React, { useState } from "react";
import { Button, Card, Title, Paragraph } from 'react-native-paper';
import { Layout } from "../../shared";
import { View, Text, Image } from "react-native";
import { COLORS, SIZES } from "../../../constants";

const data = [
  {
    address: 'Пресненская наб., 2 (этаж 1)',
    subject: 'Вызов врача-нарколога',
    date: '12.12.2022',
    speciality: 'Психиатор- нарколог',
    time: '12:45',
    comment: 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь'
  },
  {
    address: 'Пресненская набережная, 2 (этаж 1), кв. 589',
    subject: 'Вызов врача-нарколога',
    date: '12.12.2022',
    time: '12:45',
    comment: 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь, нужна детоксикация организма , возмоно психотерапевтическая помощь'
  },
]

const CardItem = ({ address, subject, date, time, comment, onAccepting } ) => {
  const [active, setActive] = useState(false)

  const onDetailedClick = () => {
    setActive(true)
  }

  const BtnChange = () => {
    if(active) {
      return <Button style={styles.btn}>Позвонить заказчику</Button>
    } else {
      return <Button onPress={onDetailedClick}>Подробнее</Button>
    }
  }

  return (
    <Card style={styles.root} children>
      <Card.Content>
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
            <Button style={styles.btn} icon={() => (
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
        <Button onPress={() => onAccepting()}>Принять</Button>
      </Card.Actions>
    </Card>
  )
}

export const Notifications = ({navigation}) => {
  const onAccepting = () => {
    navigation.navigate('Текущий вызов')
  }
  return (
    <Layout>
      <View>
        {
          data.map((item, key) => <CardItem {...item} key={key} onAccepting={onAccepting} />)
        }
      </View>
    </Layout>
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
