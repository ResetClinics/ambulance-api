import {View, Linking, Text, TouchableHighlight, Image} from "react-native";
import React, {useState} from 'react'
import {FooterItem} from "./FooterItem";

const src = "./../../../assets/favicon.png"


export const Footer = () => {
  return (
    <View style={{backgroundColor: 'white', paddingVertical: 4, bottom: 0, flexDirection: 'row', justifyContent: "space-between" }}>
      <FooterItem />
    </View>
  )
}
