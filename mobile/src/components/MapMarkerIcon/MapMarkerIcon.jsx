import React from 'react'
import { Image, StyleSheet } from 'react-native'
import markerImg from '../../../assets/images/map_marker.webp'

export const MapMarkerIcon = () => (
  <Image
    source={markerImg}
    style={styles.img}
  />
)

const styles = StyleSheet.create({
  img: {
    width: 25,
    height: 24,
  },
})
