import {View, Image, Linking, Text, TouchableHighlight} from "react-native";
import React, {useState} from 'react'
const src = "./../../../assets/favicon.png"

export const Header = () => {
  return (
    <View style={{backgroundColor: 'red', height: 100}}>
      <Image
        source={{uri: "https://hi-tech.mail.ru/news/58746-beskonechnaya-kartinka-zavirusilas-v-seti/imageset/2300004/"}}
        style={{
          width: 50,
          height: 50
        }}
      />
      <TouchableHighlight onPress={() => Linking.openURL("https://google.com")}>
        <Text>1212</Text>
      </TouchableHighlight>
    </View>
  )
}
