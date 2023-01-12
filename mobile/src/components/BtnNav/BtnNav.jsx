import {
  StyleSheet, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React from 'react'

export const BtnNav = () => (
  <View style={styles.btnNav}>
    <Button mode="outlined" style={styles.btn}>Вызовы</Button>
  </View>
)

const styles = StyleSheet.create({
  btnNav: {
    position: 'absolute',
    bottom: 10,
    right: 10
  },
})
