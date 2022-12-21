import {Image, Linking, Text, TouchableHighlight, View} from "react-native";
import React from "react";

import {COLORS, SIZES} from "../../../../constants";

export const FooterItem = () => {
  return (
    <View>
      <Image source="1111"/>
      <Text style={{ fontSize: SIZES.base, lineHeight: 12, color: COLORS.gray }}>1212</Text>
    </View>
  )
}
