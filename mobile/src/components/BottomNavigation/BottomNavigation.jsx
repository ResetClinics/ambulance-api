import {
  Image,
  StyleSheet, Text, TouchableOpacity, View,
} from 'react-native'
import React, { useState } from 'react'
import { COLORS, FONTS } from '../../../constants'
import { itemsNav } from '../../data/itemsNav'

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

export const BottomNavigation = ({ navigation, items = itemsNav }) => {
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
