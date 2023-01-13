import {
  StyleSheet,
  Text, TouchableOpacity, View
} from 'react-native'
import React from 'react'
import Ionicons from 'react-native-vector-icons/Ionicons'
import { createDrawerNavigator, DrawerContentScrollView, DrawerItem, DrawerItemList } from '@react-navigation/drawer'
import { COLORS, FONTS } from '../../../constants'
import {
  CallHistory, CurrentCall, Profile, Team
} from '../../screens'

const Drawer = createDrawerNavigator()

const CustomDrawerContent = (props, handleSignOut) => (
  <View style={styles.root}>
    {/* eslint-disable-next-line react/jsx-props-no-spreading */}
    <DrawerContentScrollView {...props}>
      <DrawerItem
        label="Иванов Иван Иванович"
        /* eslint-disable-next-line react/destructuring-assignment */
        onPress={() => props.navigation.closeDrawer()}
      />
      {/* eslint-disable-next-line react/jsx-props-no-spreading */}
      <DrawerItemList {...props} />
      <TouchableOpacity style={styles.out} onPress={() => handleSignOut()}>
        <Ionicons name="exit-outline" size={18} color={COLORS.gray} />
        <Text style={styles.text}>Выйти</Text>
      </TouchableOpacity>
    </DrawerContentScrollView>
    <Text style={styles.copyright}>© 2014-2023 Клиника Респект</Text>
  </View>
)
export const Menu = ({ handleSignOut }) => (
  // eslint-disable-next-line react/no-unstable-nested-components,react/jsx-props-no-spreading
  <Drawer.Navigator
    screenOptions={{
      drawerActiveBackgroundColor: COLORS.primary,
      drawerActiveTintColor: COLORS.white,
      drawerLabelStyle: {
        fontSize: 14,
        marginLeft: -20,
      }
    }}
    /* eslint-disable-next-line react/no-unstable-nested-components,react/jsx-props-no-spreading */
    drawerContent={(props) => <CustomDrawerContent {...props} handleSignOut={handleSignOut} />}
  >
    <Drawer.Screen
      name="team"
      component={Team}
      options={{
        // eslint-disable-next-line react/no-unstable-nested-components
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
        // eslint-disable-next-line react/no-unstable-nested-components
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
        // eslint-disable-next-line react/no-unstable-nested-components
        drawerIcon: ({ color }) => (
          <Ionicons name="person-outline" size={18} color={color} />
        ),
        title: 'Вызовы'
      }}
    />
    <Drawer.Screen
      name="profile"
      component={Profile}
      options={{
        // eslint-disable-next-line react/no-unstable-nested-components
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
  copyright: {
    textAlign: 'center',
    marginBottom: 12,
    ...FONTS.span,
  },
  text: {
    ...FONTS.span,
    color: COLORS.gray,
    marginLeft: 12
  }
})
