import React from 'react'
import { Image, StyleSheet, View } from 'react-native'
import markerImg from '../../../assets/images/map_marker.webp'

export const MapMarkerIcon = () => (
  <View>
    <Image
      source={markerImg}
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
