import React from 'react'
import { StyleSheet, View } from 'react-native'
import { COLORS } from '../../../constants'

export const ScreenLayout = ({ children }) => (
  <View style={styles.root}>
    {children}
  </View>
)

const styles = StyleSheet.create({
  root: {
    paddingBottom: 60,
    flex: 1,
    backgroundColor: COLORS.white
  }
})
