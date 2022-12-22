import {Image, Text, TouchableHighlight, View, StyleSheet} from "react-native";
import React, { useState } from "react";

import { COLORS, SIZES } from "../../../../constants";

export const FooterItem = (item) => {
  const {title, image} = item
  const [active, setActive] = useState(null)

  return (
    <TouchableHighlight style={styles.root} onPress={() => setActive(item.index)}>
      <View >
        <Image source={require('../../../images/favicon.png')} />
        <Text style={styles.text}>{title}</Text>
      </View>
    </TouchableHighlight>
  )
}


const styles = StyleSheet.create({
  root: {
    padding: 4, width: '17%', alignItems: 'center'
  },
  text: {
    fontSize: SIZES.base, lineHeight: 12, color: COLORS.gray, textAlign: 'center'
  }
})
