import React, { useEffect, useState } from 'react'
import {
  Image, StyleSheet, TextInput, TouchableOpacity, View
} from 'react-native'
import { ItemsList } from '../ItemsList'
import closeImg from '../../../../assets/images/close.png'
import { COLORS, FONTS } from '../../../../constants'

export const ModalWindow = ({ label, closeMedicineWindow, onSaveMedicine }) => {
  const [items, setItems] = useState([])
  const [searchValue, setSearchValue] = React.useState('')

  const getMedicines = async () => {
    const response = await fetch('https://ambulance.rc-respect.ru/api/medicines?page=1', {
      method: 'GET',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    })
    const json = await response.json()
    setItems(json)
  }

  useEffect(() => {
    getMedicines()
  }, [])

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
        placeholder="Поиск медикаментов"
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
    marginTop: 'auto',
    marginBottom: -21,
    position: 'relative',
    paddingTop: 15,
    marginHorizontal: -5,
    justifyContent: 'flex-end'
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
