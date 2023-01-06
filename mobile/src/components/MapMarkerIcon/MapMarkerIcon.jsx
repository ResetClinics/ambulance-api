import React from 'react'
import { Image, StyleSheet } from 'react-native'
import markerImg from '../../../assets/images/map_marker.webp'

export const MapMarkerIcon = () => (
  <Image
    resizeMode="contain"
    source={markerImg}
    style={styles.img}
  />
)

const styles = StyleSheet.create({
  img: {
    width: 24,
    height: 24,
  },
})
