import {
  Image,
  StyleSheet, Text, TouchableOpacity, View,
} from 'react-native'
import React, { useState } from 'react'
import { COLORS, FONTS } from '../../../constants'
import teamIconColor from '../../../assets/images/menu/team_color.png'
import teamIcon from '../../../assets/images/menu/team.png'
import currentCallIcon from '../../../assets/images/menu/currentCall.png'
import currentCallIconColor from '../../../assets/images/menu/currentCall_color.png'
import callHistoryIcon from '../../../assets/images/menu/callHistory.png'
import callHistoryIconColor from '../../../assets/images/menu/callHistory_color.png'
import profileIcon from '../../../assets/images/menu/profile.png'
import profileIconColor from '../../../assets/images/menu/profile_color.png'

const items = [
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
  {
    name: 'profile',
    title: 'Профиль',
    id: 4,
    icon: profileIcon,
    iconColor: profileIconColor,
  },
]

const TabChange = ({
  name, icon, title, iconColor, id, navigation, active
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

export const BottomNavigation = ({ navigation }) => {
  const [activeIndex, setActiveIndex] = useState(null)
  const setActive = (index) => {
    if (activeIndex === index) {
      setActiveIndex(null)
    } else {
      setActiveIndex(index)
    }
  }

  return (
    <View style={styles.root}>
      {
        items.map((item, key) => (
          <TabChange
          /* eslint-disable-next-line react/jsx-props-no-spreading */
            {...item}
            key={item.id}
            active={key === activeIndex}
            onClick={() => setActive(key)}
            navigation={navigation}
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
    left: 0,
    right: 0
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
