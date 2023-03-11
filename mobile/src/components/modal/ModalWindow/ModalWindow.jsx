import React, { useEffect, useState } from 'react'
import {
  Image, StyleSheet, TextInput, TouchableOpacity, View
} from 'react-native'
import { ItemsList } from '../ItemsList'
import closeImg from '../../../../assets/images/close.png'
import { COLORS, FONTS } from '../../../../constants'
import { API } from '../../../api'

export const ModalWindow = ({ label, closeMedicineWindow, onSaveMedicine }) => {
  const [items, setItems] = useState([])
  const [filterItems, setFilterItems] = useState([])
  const [searchValue, setSearchValue] = React.useState('')

  const getMedicines = async () => {
    const data = await API.medicines.index()
    setItems(data)
    setFilterItems(data)
  }

  useEffect(() => {
    getMedicines()
  }, [])

  const onChangeSearchValue = (value) => {
    setSearchValue(value)
    const result = items.filter((item) => item.name.toLowerCase().includes(value.toLowerCase()))
    setFilterItems(result)
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
      <ItemsList items={filterItems} onSave={onSaveMedicine} closeMedicineWindow={closeMedicineWindow} />
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
