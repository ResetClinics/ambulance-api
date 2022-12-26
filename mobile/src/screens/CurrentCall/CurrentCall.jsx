import { Image, Text, View } from "react-native";
import React, { useState } from "react";
import { CardLayout } from "../../components";
import { Layout } from "../../shared";
import { Button } from "react-native-paper";
import { COLORS, SIZES } from "../../../constants";

const comment = 'Мужчина ,  43 года нужна детоксикация организма , возмоно психотерапевтическая помощь',
  address = 'Пресненская наб., 2 (этаж 1)',
  subject = 'Вызов врача-нарколога',
  date = '12.12.2022',
  time = '12:45';


export const CurrentCall = () => {
  const STATUSES = {
    ASSIGNED: 'assigned',
    ROUTE: 'route'
  }
  const [status, setStatus] = useState(STATUSES.ASSIGNED);
  switch (status) {
    case STATUSES.ROUTE:
      return <Layout>
        <View style={styles.btnHolder}>
          <Button mode="outlined" raised onPress={() => setStatus(STATUSES.NOT_ASSIGNED)}>Бригада не готова к дежурству</Button>
          <Button mode="contained" style={styles.btn} onPress={() => setStatus(STATUSES.ACCEPTED)}>Бригада вышла на дежурство</Button>
        </View>
      </Layout>
    default:
      return (
        <Layout>
          <CardLayout address={address} subject={subject} date={date} time={time}>
            <View>
              <Text style={styles.info}>Коментарий к вызову:</Text>
              <Text style={styles.info}>{comment}</Text>
              <View style={styles.wrap}>
                <Button
                  style={styles.btn}
                  onPress={() => setStatus(STATUSES.ROUTE)}
                  icon={() => (
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
          </CardLayout>
          <View style={styles.btnHolder}>
            <Button mode="outlined" raised>Позвонить заказчику</Button>
            <Button mode="contained" style={styles.btn}>Бригада прибыла на вызов</Button>
          </View>
        </Layout>
      )
  }
}

const styles = {
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
    marginTop: 16
  },
  content: {
    display: 'none'
  },
  active: {
    display: 'flex'
  }
}

