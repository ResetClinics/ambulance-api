import {
  StyleSheet, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React from 'react'

export const BottomNavigation = ({ navigation }) => {
  const items = [
    {
      name: 'История вызовов',
      title: 'История вызовов',
      icon: ''
    },
    {
      name: 'История вызовов',
      title: 'История вызовов',
      icon: ''
    },
  ]
  return (
    <View style={styles.btnNav}>
      <Button
        onPress={() => navigation.navigate('История вызовов')}
        mode="outlined"
        style={styles.btn}
      >
        Вызовы
      </Button>
    </View>
  )
}

const styles = StyleSheet.create({
  btnNav: {
    position: 'absolute',
    bottom: 10,
    right: 10
  },
})
