import {Image, Linking, Text, TouchableHighlight, View} from "react-native";
import React, {useEffect, useState} from "react";

import {COLORS, SIZES} from "../../../../constants";

export const FooterItem = (item) => {
  const [active, setActive] = useState(null)

  return (
    <TouchableHighlight style={{padding: 4, width: '17%', alignItems: 'center'}} onPress={() => setActive(item.index)}>
      <View >
        <Image source={{uri: item.image}} />
        <Text style={{ fontSize: SIZES.base, lineHeight: 12, color: COLORS.gray, textAlign: 'center' }}>{item.title}</Text>
      </View>
    </TouchableHighlight>
  )
}
