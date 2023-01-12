import {
  Image,
  StyleSheet, View
} from 'react-native'
import { Button } from 'react-native-paper'
import React from 'react'
import plusImg from '../../../assets/images/plusColor.png'
import { COLORS } from '../../../constants'

export const BottomNavigation = ({ navigation }) => {
  const items = [
    {
      name: 'История вызовов',
      title: 'История вызовов',
      icon: '',
      id: 1
    },
    {
      name: 'История вызовов',
      title: 'История вызовов',
      icon: '',
      id: 2
    },
  ]
  return (
    <View style={styles.root}>
      {
        items.map(({ name, title, id }) => (
          <Button
            onPress={() => navigation.navigate(name)}
            key={id}
            /* eslint-disable-next-line react/no-unstable-nested-components */
            icon={() => (
              <Image
                source={plusImg}
                style={styles.img}
              />
            )}
          >
            {title}
          </Button>
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
    backgroundColor: COLORS.gray,
    paddingHorizontal: 16,
    paddingVertical: 8,
    maxHeight: 60,
    alignItems: 'center',
    justifyContent: 'center',
    flexDirection: 'row',
    left: 0,
    right: 0
  },
  img: {
    width: 24, height: 24
  },
})
