import { Card, Paragraph, Title } from "react-native-paper";
import { StyleSheet, Text, View } from "react-native";
import React from "react";
import { COLORS, FONTS } from "../../../constants";

export const CardLayout = ({ address, subject, date, time, children }) => {
  return (
    <Card style={styles.root} children>
      <Card.Content>
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
        {
          children
        }
      </Card.Content>
    </Card>
  )
}

const styles = StyleSheet.create({
  root: {
    backgroundColor: COLORS.white,
    borderRadius: 4,
    borderColor: COLORS.primary,
    borderWidth: 1,
    marginBottom: 16
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
  text: {
    width: '45%',
    ...FONTS.smallText
  },
});
