import { Card, Paragraph, Title} from "react-native-paper";
import { Text, View } from "react-native";
import React from "react";
import { COLORS, SIZES } from "../../../constants";

export const CardLayout = ({ address, subject, date, time, children }) => {
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
        {
          children
        }
      </Card.Content>
    </Card>
  )
}

const styles = {
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 16
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
}
