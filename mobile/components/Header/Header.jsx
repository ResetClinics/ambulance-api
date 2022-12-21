import { View, Image, Linking, Text, TouchableHighlight, StyleSheet } from "react-native";
import React from 'react'

export const Header = () => {
  return (
    <View style={styles.root}>
      <Image
        style={styles.img}
        source={require('./favicon.png')}
      />
      <TouchableHighlight onPress={() => Linking.openURL("https://google.com")}>
        <Text>1212</Text>
      </TouchableHighlight>
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    backgroundColor: 'red', height: 100
  },
  img: {
    width: 48,
    height: 48
  }
})
