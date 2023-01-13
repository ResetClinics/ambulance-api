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
    name: 'сallHistory',
    title: 'Вызовы',
    id: 3,
    icon: callHistoryIcon,
    iconColor: callHistoryIconColor,
  },
]

const TabChange = ({
  name, icon, title, iconColor, id, navigation, active,
}) => {
  if (active) {
    return (
      <TouchableOpacity style={styles.btn} onPress={() => navigation.navigate(name)} key={id}>
        <Image
          source={iconColor}
          style={styles.img}
        />
        <Text style={styles.text}>{title}</Text>
      </TouchableOpacity>
    )
  }
  return (
    <TouchableOpacity style={styles.btn} onPress={() => navigation.navigate(name)} key={id}>
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
    paddingHorizontal: 16,
    paddingVertical: 8,
    maxHeight: 60,
    alignItems: 'center',
    justifyContent: 'space-between',
    flexDirection: 'row',
    borderTopWidth: 1,
    borderTopColor: COLORS.light
  },
  img: {
    width: 24,
    height: 24,
    marginBottom: 8
  },
  btn: {
    flexDirection: 'column',
    alignItems: 'center',
  },
  text: {
    ...FONTS.small
  }
})
