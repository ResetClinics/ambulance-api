import { View, StyleSheet } from "react-native";
import React from 'react';
import { FooterItem } from "./FooterItem";
import { footerItems } from "./data/footerItems";

const src = "./../../../assets/favicon.png"


export const Footer = () => {
  return (
    <View style={styles.root}>
      {
        footerItems.map((item, key) => <FooterItem {...item} key={key}/>)
      }
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    backgroundColor: 'white', flexDirection: 'row', justifyContent: 'space-between', width: '100%', paddingRight: '2%'
  }
})
