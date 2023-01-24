import React from 'react'
import { Image, StyleSheet } from 'react-native'
import closeImg from '../../../assets/images/close.png'

export const CloseIcon = () => (
  <Image
    resizeMode="contain"
    source={closeImg}
    style={styles.img}
  />
)

const styles = StyleSheet.create({
  img: {
    width: 24,
    height: 24,
  },
})
