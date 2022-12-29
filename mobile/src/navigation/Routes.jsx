import React from "react";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Image, StyleSheet } from "react-native";
import { COLORS } from "../../constants";
import { CallHistory, Chat, CurrentCall, Notifications, Profile, Team } from "../screens";

const Tab = createBottomTabNavigator();

const teamIcon = '../../assets/images/menu/team.webp'
const teamIcon_color = '../../assets/images/menu/team_color.webp'
const currentCallIcon = '../../assets/images/menu/currentCall.webp'
const currentCallIcon_color = '../../assets/images/menu/currentCall_color.webp'
const callHistoryIcon = '../../assets/images/menu/callHistory.webp'
const callHistoryIcon_color = '../../assets/images/menu/callHistory_color.webp'
const chatIcon = '../../assets/images/menu/chat.webp'
const chatIcon_color = '../../assets/images/menu/chat_color.webp'
const notificationIcon = '../../assets/images/menu/notification.webp'
const notificationIcon_color = '../../assets/images/menu/notification_color.webp'
const profileIcon = '../../assets/images/menu/profile.webp'
const profileIcon_color = '../../assets/images/menu/profile_color.webp'

const icons = {
  "Бригада": {
    default: require(teamIcon),
    focused: require(teamIcon_color),
  },
  "Текущий вызов": {
    default: require(currentCallIcon),
    focused: require(currentCallIcon_color),
  },
  "История вызовов": {
    default: require(callHistoryIcon),
    focused: require(callHistoryIcon_color),
  },
  "Чат": {
    default: require(chatIcon),
    focused: require(chatIcon_color),
  },
  "Уведомления": {
    default: require(notificationIcon),
    focused: require(notificationIcon_color),
  },
  "Профиль": {
    default: require(profileIcon),
    focused: require(profileIcon_color),
  },
  "default": {
    default: require(teamIcon),
    focused: require(teamIcon_color),
  }
}

const tabBarIcon = (focused, color, size, route) => {
  let currentIcons;
  if (icons.hasOwnProperty(route.name)) {
    currentIcons = icons[route.name]
  } else {
    currentIcons = icons["default"]
  }
  let iconName = focused ? currentIcons.focused : currentIcons.default
  return <Image source={iconName} size={size} color={color} style={styles.img} />
}

export const Routes = () => {
  return (
    <Tab.Navigator
      screenOptions={({route}) => {
        return ({
            tabBarStyle: {paddingTop: 4, height: 60, paddingBottom: 10},
            tabBarIcon: ({focused, color, size}) => tabBarIcon(focused, color, size, route),
            tabBarActiveTintColor: COLORS.primary,
            tabBarInactiveTintColor: COLORS.gray,
          })
        }
      }
    >
      <Tab.Screen name="Бригада" component={Team} />
      <Tab.Screen name="Текущий вызов" component={CurrentCall} />
      <Tab.Screen name="История вызовов" component={CallHistory} />
      <Tab.Screen name="Чат" component={Chat} />
      <Tab.Screen name="Уведомления" component={Notifications} options={{tabBarBadge: 3}} />
      <Tab.Screen name="Профиль" component={Profile} />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  img: {
    width: 24,
    height: 24,
  }
});
