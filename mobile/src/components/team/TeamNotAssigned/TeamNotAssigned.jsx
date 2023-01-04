import {StyleSheet, Text} from "react-native";
import { IconButton } from "react-native-paper";
import { COLORS } from "../../../../constants";
import React from "react";
import { Layout } from "../../Layout";

export const TeamNotAssigned = ({onPress}) => {
  return (
    <Layout>
      <Text style={styles.text}>Бригада еще не сформирована</Text>
      <IconButton
        loading
        style={styles.img}
        mode='contained'
        icon="reload"
        iconColor={COLORS.white}
        containerColor={COLORS.blue}
        size={35}
        onPress={onPress}
      />
    </Layout>
  )
}

const styles = StyleSheet.create({
  img: {
    width: 48,
    height: 48,
    marginLeft: 'auto',
    marginRight: 'auto',
    marginTop: 24
  },
  text: {
    fontSize: 30,
    color: COLORS.black,
    fontFamily: 'Roboto-Medium',
    lineHeight: 40
  },
});
