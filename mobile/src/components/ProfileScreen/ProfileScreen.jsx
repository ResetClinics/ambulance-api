import { StyleSheet, Text } from "react-native";
import { Avatar } from "react-native-paper";
import React from "react";
import { COLORS, FONTS, SIZES } from "../../../constants";

const defaultImg = require('../../../assets/images/default.webp')
const img = require('../../../assets/images/image.webp')

export const ProfileScreen = (/*{img}*/) => {
  return (
    <>
      <Avatar.Image size={140} source={img || defaultImg} style={styles.root} />
      <Text style={styles.title}>Иванов Иван Иванович</Text>
      <Text style={styles.text}>Невролог-терапевт</Text>
    </>
  )
}

const styles = StyleSheet.create({
  title: {
    textAlign: 'center',
    fontSize: SIZES.fs18,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
    marginBottom: 16,
    ...FONTS.h2,
  },
  text: {
    textAlign: 'center',
    fontSize: SIZES.fs16,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
    fontFamily: 'Roboto-Italic'
  },
  root: {
    marginLeft: 'auto',
    marginRight: 'auto',
    marginBottom: 16,
    backgroundColor: 'transparent'
  }
});
