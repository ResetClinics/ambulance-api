import React from "react";
import { COLORS, FONTS } from "../../../../constants";
import { Image, StyleSheet, Text, View } from "react-native";
import { Button } from "react-native-paper";

export const ModalList = ({data}) => {
  return (
    <View style={styles.root}>
      {
        data.slice(0, 7).map((item, key) =>
          <View style={styles.item} key={key}>
            <Text style={styles.itemText}>{item.name}</Text>
            <Image
              source={require('../../../../assets/images/plusColor.webp')}
              style={{ width: 14, height: 14 }}
            />
          </View>
        )
      }
      <View style={styles.holder}>
        <Button  mode="outlined" style={styles.btn}>Сбросить</Button>
        <Button mode="contained" style={styles.btn}>Сохранить</Button>
      </View>
    </View>
  )
}
const styles = StyleSheet.create({
  root: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    marginTop: 16,
    padding: 10
  },
  item: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 18,
  },
  itemText: {
    ...FONTS.text,
    color: COLORS.primary
  },
  holder: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 18
  },
  btn: {
    width: '47%'
  }
});
