import React from 'react'
import { Image, StyleSheet } from 'react-native'
import logoImg from '../../../assets/logo.webp'

export const Logo = () => (
  <Image
    resizeMode="contain"
    source={logoImg}
    style={styles.img}
  />
)

const styles = StyleSheet.create({
  img: {
    width: 'auto',
    minWidth: 116,
    maxHeight: 116,
    marginBottom: '20%',
    marginTop: '30%',
  }
})
