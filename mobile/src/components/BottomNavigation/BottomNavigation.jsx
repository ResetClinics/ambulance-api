import {
  Image,
  StyleSheet, Text, TouchableOpacity, View,
} from 'react-native'
import React from 'react'
import { COLORS, FONTS } from '../../../constants'
import teamIcon from '../../../assets/images/menu/team.png'
import teamIconColor from '../../../assets/images/menu/team_color.png'
import currentCallIcon from '../../../assets/images/menu/currentCall.png'
import currentCallIconColor from '../../../assets/images/menu/currentCall_color.png'
import callHistoryIcon from '../../../assets/images/menu/callHistory.png'
import callHistoryIconColor from '../../../assets/images/menu/callHistory_color.png'
import chatIcon from '../../../assets/images/menu/chat.png'
import chatIconColor from '../../../assets/images/menu/chat_color.png'

const itemsNav = [
  {
    name: 'team',
    title: 'Бригада',
    id: 1,
    icon: teamIcon,
    iconColor: teamIconColor
  },
  {
    name: 'сurrentCall',
    title: 'Текущий вызов',
    id: 2,
    icon: currentCallIcon,
    iconColor: currentCallIconColor,
  },
  {
    name: 'сalls',
    title: 'Вызовы',
    id: 3,
    icon: callHistoryIcon,
    iconColor: callHistoryIconColor,
  },
  {
    name: 'chat',
    title: 'Чат',
    id: 4,
    icon: chatIcon,
    iconColor: chatIconColor,
  },
]

const TabChange = ({
  name, title, icon, iconColor, navigation, active,
}) => {
  if (active) {
    return (
      <TouchableOpacity
        activeOpacity={1}
        style={styles.btn}
        onPress={() => navigation.navigate(name)}
      >
        <Image
          source={iconColor}
          style={styles.img}
        />
        <Text style={[styles.text, styles.textColor]}>{title}</Text>
      </TouchableOpacity>
    )
  }
  return (
    <TouchableOpacity
      activeOpacity={1}
      style={styles.btn}
      onPress={() => navigation.navigate(name)}
    >
      <Image
        source={icon}
        style={styles.img}
      />
      <Text style={styles.text}>{title}</Text>
    </TouchableOpacity>
  )
}

export const BottomNavigation = ({ navigation, items = itemsNav }) => {
  const { index, routeNames } = navigation.getState()
  const currentRouteName = routeNames[index]

  return (
    <View style={styles.root}>
      {
        items.map((item) => (
          <TabChange
            /* eslint-disable-next-line react/jsx-props-no-spreading */
            {...item}
            key={item.id}
            navigation={navigation}
            currentRouteName={currentRouteName}
            active={currentRouteName === item.name}
          />
        ))
      }
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: COLORS.white,
    paddingVertical: 8,
    justifyContent: 'space-between',
    flexDirection: 'row',
    shadowColor: COLORS.black,
    shadowOffset: {
      width: 0,
      height: 12,
    },
    shadowOpacity: 0.58,
    shadowRadius: 16.00,
    elevation: 24,
  },
  img: {
    width: 24,
    height: 24,
    marginBottom: 8
  },
  btn: {
    flexDirection: 'column',
    alignItems: 'center',
    flex: 1,
  },
  text: {
    ...FONTS.small,
    textAlign: 'center'
  },
  textColor: {
    color: COLORS.primary
  }
})
