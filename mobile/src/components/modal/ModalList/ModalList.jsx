import React from 'react'
import {
  Image, StyleSheet, Text, View
} from 'react-native'
import { Button } from 'react-native-paper'
import { COLORS, FONTS } from '../../../../constants'
import plusImg from '../../../../assets/images/plusColor.png'

export const ModalList = ({ data }) => (
  <View style={styles.root}>
    {
        data.slice(0, 7).map(({ id, name }) => (
          <View style={styles.item} key={id}>
            <Text style={styles.itemText}>{name}</Text>
            <Image
              source={plusImg}
              style={styles.img}
            />
          </View>
        ))
      }
    <View style={styles.holder}>
      <Button mode="outlined" style={styles.btn}>Сбросить</Button>
      <Button mode="contained" style={styles.btn}>Сохранить</Button>
    </View>
  </View>
)
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
  },
  img: {
    width: 14, height: 14
  }
})
