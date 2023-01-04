import React from 'react'
import { StyleSheet, View } from 'react-native'
import { COLORS } from '../../../constants'

export const Layout = ({ children }) => (
  <View style={styles.root}>
    {children}
  </View>
)

const styles = StyleSheet.create({
  root: {
    padding: 16,
    flex: 1,
    backgroundColor: COLORS.white
  }
})
