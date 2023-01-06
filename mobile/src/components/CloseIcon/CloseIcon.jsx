import React from 'react'
import { Image, StyleSheet, View } from 'react-native'
import closeImg from '../../../assets/images/close.webp'

export const CloseIcon = () => (
  <View>
    <Image
      source={closeImg}
      style={styles.img}
    />
  </View>
)

const styles = StyleSheet.create({
  img: {
    width: 25,
    height: 24,
  },
})
