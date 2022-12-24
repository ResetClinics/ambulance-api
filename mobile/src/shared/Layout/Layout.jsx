import React from 'react'
import { View } from "react-native";
import { COLORS } from "../../../constants";

export const Layout  = ({ children} ) => {
  return (
    <View style={styles.root}>
      {children}
    </View>
  )
}

const styles = {
  root: {
    padding: 16,
    flex: 1,
    backgroundColor: COLORS.white
  }
}
