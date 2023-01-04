import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Image } from 'react-native'
import { COLORS } from '../../constants'
import {
  CallHistory, CurrentCall, Notifications, Profile, Team
} from '../screens'
import teamIcon from '../../assets/images/menu/team.webp'
import teamIconColor from '../../assets/images/menu/team_color.webp'
import currentCallIcon from '../../assets/images/menu/currentCall.webp'
import currentCallIconColor from '../../assets/images/menu/currentCall_color.webp'
import callHistoryIcon from '../../assets/images/menu/callHistory.webp'
import callHistoryIconColor from '../../assets/images/menu/callHistory_color.webp'
import notificationIcon from '../../assets/images/menu/notification.webp'
import notificationIconColor from '../../assets/images/menu/notification_color.webp'
import profileIcon from '../../assets/images/menu/profile.webp'
import profileIconColor from '../../assets/images/menu/profile_color.webp'

const Tab = createBottomTabNavigator()

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
  Уведомления: {
    default: notificationIcon,
    focused: notificationIconColor,
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
  return <Image source={iconName} size={size} color={color} style={{ width: 24, height: 24 }} />
}

export const Routes = () => (
  <Tab.Navigator
    screenOptions={({ route }) => ({
      tabBarStyle: { paddingTop: 4, height: 60, paddingBottom: 10 },
      tabBarIcon: ({ focused, color, size }) => tabBarIcon(focused, color, size, route),
      tabBarActiveTintColor: COLORS.primary,
      tabBarInactiveTintColor: COLORS.gray,
    })}
  >
    <Tab.Screen name="Бригада" component={Team} />
    <Tab.Screen name="Текущий вызов" component={CurrentCall} />
    <Tab.Screen name="История вызовов" component={CallHistory} />
    <Tab.Screen name="Уведомления" component={Notifications} options={{ tabBarBadge: 3 }} />
    <Tab.Screen name="Профиль" component={Profile} />
  </Tab.Navigator>
)
