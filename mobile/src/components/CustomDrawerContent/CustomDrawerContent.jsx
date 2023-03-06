/* eslint-disable */
import React from 'react'
import {
  StyleSheet, Text, TouchableOpacity, View
} from 'react-native'
import { DrawerContentScrollView, DrawerItemList } from '@react-navigation/drawer'
import Ionicons from 'react-native-vector-icons/Ionicons'
import { COLORS, FONTS } from '../../../constants'

export const CustomDrawerContent = (props) => (
  <View style={styles.root}>
    <DrawerContentScrollView {...props}>
      <TouchableOpacity
        style={styles.outUser}
        onPress={() => props.navigation.closeDrawer()}
        activeOpacity={1}
      >
        <Text style={[styles.text, styles.black]}>Иванов Иван Иванович</Text>
        <Ionicons name="arrow-forward" size={20} color={COLORS.black} />
      </TouchableOpacity>
      <DrawerItemList {...props} />
      <TouchableOpacity
        style={styles.out}
        onPress={() => props.handleSignOut()}
        activeOpacity={1}
      >
        <Ionicons name="exit-outline" size={24} color={COLORS.gray} />
        <Text style={styles.text}>Выйти</Text>
      </TouchableOpacity>
    </DrawerContentScrollView>
    <Text style={styles.copyright}>© 2014-2023 Клиника Респект</Text>
  </View>
)

const styles = StyleSheet.create({
  root: {
    flex: 1
  },
  outUser: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 12,
    paddingTop: 7,
    paddingBottom: 5,
    width: '100%'
  },
  copyright: {
    textAlign: 'center',
    marginBottom: 12,
    ...FONTS.span,
  },
  text: {
    ...FONTS.span,
    color: COLORS.gray,
    marginLeft: 12
  },
  out: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 18,
    paddingHorizontal: 12,
    marginLeft: 14,
    width: '94%',
    borderTopColor: COLORS.darkGrey,
    borderTopWidth: 1
  },
  black: {
    color: COLORS.black,
    width: '80%',
    textAlignVertical: 'center',
  }
})
