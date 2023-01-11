import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import {
  Image, StyleSheet, View, Text
} from 'react-native'
import { createStackNavigator } from '@react-navigation/stack'
import { getFocusedRouteNameFromRoute, NavigationContainer } from '@react-navigation/native'
import {
  createDrawerNavigator, DrawerContentScrollView, DrawerItemList
} from '@react-navigation/drawer'

import Ionicons from 'react-native-vector-icons/Ionicons'
import { COLORS } from '../../constants'
import {
  CallHistory, CurrentCall, PasswordForgetScreen, Profile, Sent, SignInScreen, Team
} from '../screens'
import teamIcon from '../../assets/images/menu/team.png'
import teamIconColor from '../../assets/images/menu/team_color.png'
import currentCallIcon from '../../assets/images/menu/currentCall.png'
import currentCallIconColor from '../../assets/images/menu/currentCall_color.png'
import callHistoryIcon from '../../assets/images/menu/callHistory.png'
import callHistoryIconColor from '../../assets/images/menu/callHistory_color.png'
import profileIcon from '../../assets/images/menu/profile.png'
import profileIconColor from '../../assets/images/menu/profile_color.png'

const Tab = createBottomTabNavigator()
const RootStack = createStackNavigator()
const Drawer = createDrawerNavigator()

const icons = {
  Бригада: {
    default: teamIcon,
    focused: teamIconColor,
  },
  'Текущий вызов': {
    default: currentCallIcon,
    focused: currentCallIconColor,
  },
  'История вызовов': {
    default: callHistoryIcon,
    focused: callHistoryIconColor,
  },
  Профиль: {
    default: profileIcon,
    focused: profileIconColor,
  },
  default: {
    default: teamIcon,
    focused: teamIconColor,
  }
}

const tabBarIcon = (focused, color, size, route) => {
  let currentIcons
  // eslint-disable-next-line no-prototype-builtins
  if (icons.hasOwnProperty(route.name)) {
    currentIcons = icons[route.name]
  } else {
    currentIcons = icons.default
  }
  const iconName = focused ? currentIcons.focused : currentIcons.default
  return <Image source={iconName} size={size} color={color} style={styles.img} />
}

const AppNavigator = () => (
  <Tab.Navigator
    screenOptions={({ route }) => ({
      tabBarStyle: { paddingTop: 4, height: 60, paddingBottom: 10 },
      tabBarIcon: ({ focused, color, size }) => tabBarIcon(focused, color, size, route),
      tabBarActiveTintColor: COLORS.primary,
      tabBarInactiveTintColor: COLORS.gray,
      headerShown: false
    })}
  >
    <Tab.Screen
      name="Бригада"
      component={Team}
    />
    <Tab.Screen
      name="Текущий вызов"
      component={CurrentCall}
    />
    <Tab.Screen
      name="История вызовов"
      component={CallHistory}
    />
    <Tab.Screen
      name="Профиль"
      component={Profile}
    />
  </Tab.Navigator>
)

const CustomDrawerContent = (props) => (
  <View style={{ flex: 1 }}>
    <DrawerContentScrollView {...props}>
      <DrawerItemList {...props} />
      <View style={{ flexDirection: 'row', alignItems: 'center'}}>
        <Ionicons name="exit-outline" size={18} />
        <Text>Выйти</Text>
      </View>
    </DrawerContentScrollView>
    <Text>2014-2023 Клиника Респект</Text>
  </View>
)

const HomeDrawer = ({ handleSignOut }) => (
  // eslint-disable-next-line react/no-unstable-nested-components,react/jsx-props-no-spreading
  <Drawer.Navigator
    screenOptions={{
      drawerActiveBackgroundColor: COLORS.primary,
      drawerActiveTintColor: COLORS.white,
      drawerLabelStyle: {
        fontSize: 16,
        marginLeft: -20,
      }
    }}
    drawerContent={(props) => <CustomDrawerContent {...props} handleSignOut={handleSignOut} />}
  >
    <Drawer.Screen
      name="Home"
      component={AppNavigator}
      options={({ route }) => ({
        headerTitle: getFocusedRouteNameFromRoute(route),
        // eslint-disable-next-line react/no-unstable-nested-components
        drawerIcon: ({ color }) => (
          <Ionicons name="home-outline" size={18} color={color} />
        )
      })}
    />
    <Drawer.Screen
      name="Профиль"
      component={Profile}
      options={{
        // eslint-disable-next-line react/no-unstable-nested-components
        drawerIcon: ({ color }) => (
          <Ionicons name="person-outline" size={18} color={color} />
        )
      }}
    />
  </Drawer.Navigator>
)
export const Routes = () => {
  const [isAuthenticated, setIsAuthenticated] = React.useState(false)

  const handleSignIn = () => {
    setIsAuthenticated(true)
  }

  const handleSignOut = () => {
    setIsAuthenticated(false)
  }

  return (
    <NavigationContainer>
      <RootStack.Navigator>
        {isAuthenticated ? (
          <RootStack.Screen
            name="App"
            component={HomeDrawer}
            options={{ headerShown: false }}
            handleSignOut={handleSignOut}
          />
        ) : (
          <>
            <RootStack.Screen
              name="Sign In"
              options={{ headerShown: false }}
            >
              {(props) => (
                // eslint-disable-next-line react/jsx-props-no-spreading
                <SignInScreen {...props} onSignIn={handleSignIn} />
              )}
            </RootStack.Screen>
            <RootStack.Screen
              name="Восстановление пароля"
              component={PasswordForgetScreen}
            />
            <RootStack.Screen
              name="isSent"
              component={Sent}
              options={{ headerShown: false }}
            />
          </>
        )}
      </RootStack.Navigator>
    </NavigationContainer>
  )
}

const styles = StyleSheet.create({
  img: {
    width: 24, height: 24
  },
})
