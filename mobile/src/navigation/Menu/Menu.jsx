/* eslint-disable */
import {
  StyleSheet,
  Text, TouchableOpacity, View
} from 'react-native'
import React from 'react'
import Ionicons from 'react-native-vector-icons/Ionicons'
import {
  createDrawerNavigator, DrawerContentScrollView, DrawerItem, DrawerItemList
} from '@react-navigation/drawer'
import { COLORS, FONTS } from '../../../constants'
import {
  CallHistory, CurrentCall, Profile, Team
} from '../../screens'

const Drawer = createDrawerNavigator()

const CustomDrawerContent = (props) => (
  <View style={styles.root}>
    <DrawerContentScrollView {...props}>
      <TouchableOpacity
        style={styles.outUser}
        onPress={() => props.navigation.closeDrawer()}
        activeOpacity={1}
      >
        <Ionicons name="arrow-back" size={20} color={COLORS.black} />
        <Text style={[styles.text, styles.black]}>Иванов Иван</Text>
      </TouchableOpacity>
      <DrawerItemList {...props} />
      <TouchableOpacity
        style={styles.out}
        onPress={() => props.handleSignOut()}
        activeOpacity={1}
      >
        <Ionicons name="exit-outline" size={18} color={COLORS.gray} />
        <Text style={styles.text}>Выйти</Text>
      </TouchableOpacity>
    </DrawerContentScrollView>
    <Text style={styles.copyright}>© 2014-2023 Клиника Респект</Text>
  </View>
)
export const Menu = ({ handleSignOut }) => (
  <Drawer.Navigator
    screenOptions={{
      drawerActiveBackgroundColor: COLORS.primary,
      drawerActiveTintColor: COLORS.white,
      drawerLabelStyle: {
        fontSize: 14,
        marginLeft: -20,
      },
      overlayColor: COLORS.overlay
    }}
    drawerContent={(props) => <CustomDrawerContent {...props} handleSignOut={handleSignOut} />}
  >
    <Drawer.Screen
      name="team"
      component={Team}
      options={{
        drawerIcon: ({ color }) => (
          <Ionicons name="person-outline" size={18} color={color} />
        ),
        title: 'Бригада'
      }}
    />
    <Drawer.Screen
      name="сurrentCall"
      component={CurrentCall}
      options={{
        drawerIcon: ({ color }) => (
          <Ionicons name="person-outline" size={18} color={color} />
        ),
        title: 'Текущий вызов'
      }}
    />
    <Drawer.Screen
      name="сallHistory"
      component={CallHistory}
      options={{
        drawerIcon: ({ color }) => (
          <Ionicons name="ios-time-outline" size={18} color={color} />
        ),
        title: 'Вызовы'
      }}
    />
    <Drawer.Screen
      name="profile"
      component={Profile}
      options={{
        drawerIcon: ({ color }) => (
          <Ionicons name="person-outline" size={18} color={color} />
        ),
        title: 'Профиль'
      }}
    />
  </Drawer.Navigator>
)

const styles = StyleSheet.create({
  root: {
    flex: 1
  },
  out: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 18,
    paddingHorizontal: 12,
    marginLeft: 8,
    width: '94%',
    borderTopColor: COLORS.darkGrey,
    borderTopWidth: 1
  },
  outUser: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    marginLeft: 5,
    paddingVertical: 18,
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
  black: {
    color: COLORS.black,
  }
})
