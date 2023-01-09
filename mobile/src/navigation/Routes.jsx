import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Image, StyleSheet } from 'react-native'
import { createStackNavigator } from '@react-navigation/stack'
import { getFocusedRouteNameFromRoute, NavigationContainer } from '@react-navigation/native'
import { COLORS } from '../../constants'
import {
  CallHistory, CurrentCall, PasswordForgetScreen, Profile, SignInScreen, Team
} from '../screens'
import teamIcon from '../../assets/images/menu/team.webp'
import teamIconColor from '../../assets/images/menu/team_color.webp'
import currentCallIcon from '../../assets/images/menu/currentCall.webp'
import currentCallIconColor from '../../assets/images/menu/currentCall_color.webp'
import callHistoryIcon from '../../assets/images/menu/callHistory.webp'
import callHistoryIconColor from '../../assets/images/menu/callHistory_color.webp'
import profileIcon from '../../assets/images/menu/profile.webp'
import profileIconColor from '../../assets/images/menu/profile_color.webp'

const Tab = createBottomTabNavigator()
const RootStack = createStackNavigator()

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
    })}
  >
    <Tab.Screen name="Бригада" component={Team} />
    <Tab.Screen
      name="Текущий вызов"
      component={CurrentCall}
      options={{ headerShown: false }}
    />
    <Tab.Screen name="История вызовов" component={CallHistory} />
    <Tab.Screen name="Профиль" component={Profile} />
  </Tab.Navigator>
)

export const Routes = () => {
  const [isAuthenticated, setIsAuthenticated] = React.useState(false)

  const handleSignIn = () => {
    setIsAuthenticated(true)
  }

  return (
    <NavigationContainer>
      <RootStack.Navigator>
        {isAuthenticated ? (
          <RootStack.Screen
            name="App"
            component={AppNavigator}
            options={{ headerShown: false }}
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
              name="Password Forget"
              component={PasswordForgetScreen}
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
