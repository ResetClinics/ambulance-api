import React from 'react'
import {
  FlatList,
  StyleSheet, Text, View
} from 'react-native'
import { Button, IconButton } from 'react-native-paper'
import { COLORS, FONTS } from '../../../../constants'

const ListItem = (item) => {
  const { name } = item
  return (
    <View style={styles.item}>
      <Text style={styles.itemText}>{name}</Text>
      <IconButton
        icon="plus"
        iconColor={COLORS.primary}
        containerColor={COLORS.white}
        size={20}
        onPress={() => console.log('Pressed')}
      />
    </View>
  )
}

export const ItemsList = ({ items }) => (
  <View style={styles.root}>
    <FlatList
      style={styles.wrap}
      data={items}
      /* eslint-disable-next-line react/jsx-props-no-spreading */
      renderItem={({ item }) => <ListItem {...item} />}
    />
    <View style={styles.holder}>
      <Button mode="outlined" style={styles.btn}>Сбросить</Button>
      <Button mode="contained" style={styles.btn}>Сохранить</Button>
    </View>
  </View>
)
const styles = StyleSheet.create({
  root: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    marginTop: 16,
    padding: 10,
  },
  item: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingLeft: 18,
    paddingRight: 5,
  },
  itemText: {
    ...FONTS.text,
    color: COLORS.primary
  },
  holder: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 25,
    paddingBottom: 10,
    paddingTop: 18,
    marginTop: 'auto',
    backgroundColor: COLORS.white,
    borderTopColor: COLORS.light,
    borderTopWidth: 1,
    marginHorizontal: -10
  },
  btn: {
    width: '47%'
  },
  wrap: {
    maxHeight: 300,
  }
})
