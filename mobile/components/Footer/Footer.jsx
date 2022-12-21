import {View, Linking, Text, TouchableHighlight, Image} from "react-native";
import React, {useState} from 'react'
import {FooterItem} from "./FooterItem";
import {footerItems} from "./data/footerItems";

const src = "./../../../assets/favicon.png"


export const Footer = () => {
  return (
    <View style={{backgroundColor: 'white', flexDirection: 'row', justifyContent: 'space-between', width: '100%', paddingRight: '2%' }}>
      {
        footerItems.map((item, key) => <FooterItem {...item} key={key}/>)
      }
    </View>
  )
}
