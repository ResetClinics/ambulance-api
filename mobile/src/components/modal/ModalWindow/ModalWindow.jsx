import React, { useState } from 'react'
import {
  Image, StyleSheet, TextInput, TouchableOpacity, View
} from 'react-native'
import { ItemsList } from '../ItemsList'
import closeImg from '../../../../assets/images/close.png'
import { COLORS, FONTS } from '../../../../constants'
import { getItems } from '../ItemsList/data/data'

export const ModalWindow = ({ label, closeMedicineWindow, onSaveMedicine }) => {
  const [items, setItems] = useState(getItems())
  const [searchValue, setSearchValue] = React.useState('')

  const onChangeSearchValue = (value) => {
    setSearchValue(value)
    const result = items.filter((item) => item.name.toLowerCase().includes(value.toLowerCase()))
    setItems(result)
  }

  return (
    <View style={styles.container}>
      <TouchableOpacity
        onPress={closeMedicineWindow}
        activeOpacity={1}
      >
        <Image
          source={closeImg}
          style={styles.img}
          resizeMode="contain"
        />
      </TouchableOpacity>
      <TextInput
        style={styles.input}
        placeholder={label}
        label={label}
        value={searchValue}
        onChangeText={onChangeSearchValue}
      />
      <ItemsList items={items} onSave={onSaveMedicine} closeMedicineWindow={closeMedicineWindow} />
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: COLORS.transparent,
    margin: 16,
    position: 'relative',
    paddingVertical: 15
  },
  img: {
    width: 30,
    height: 30,
    marginLeft: 'auto',
    marginBottom: 15,
  },
  input: {
    ...FONTS.text,
    padding: 15,
    borderRadius: 4,
    backgroundColor: COLORS.white,
    width: '100%',
    maxHeight: 48
  }
})
