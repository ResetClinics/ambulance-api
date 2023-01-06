import React from 'react'
import { Image, StyleSheet } from 'react-native'
import closeImg from '../../../assets/images/close.webp'

export const CloseIcon = () => (
  <Image
    source={closeImg}
    style={styles.img}
  />
)

const styles = StyleSheet.create({
  img: {
    width: 25,
    height: 24,
  },
})
