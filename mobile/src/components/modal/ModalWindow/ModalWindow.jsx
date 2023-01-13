import React, { useState } from 'react'
import { Image, StyleSheet, View } from 'react-native'
import { magicModal } from 'react-native-magic-modal'
import { Button } from 'react-native-paper'
import { ModalInput } from '../ModalInput'
import { ModalList } from '../ModalList'
import closeImg from '../../../../assets/images/close.png'
import { COLORS } from '../../../../constants'
import { setList } from '../ModalList/data/data'

const data = setList()

const CloseImg = () => (
  <Image
    source={closeImg}
    style={styles.img}
  />
)

export const ModalWindow = ({ label }) => {
  const [items, setItems] = useState(data)
  const [searchValue, setSearchValue] = React.useState('')

  const onChangeSearchValue = (value) => {
    setSearchValue(value)
    const result = items.filter((item) => item.name.toLowerCase().includes(value.toLowerCase()))
    setItems(result)
  }

  return (
    <View style={styles.container}>
      <Button
        onPress={() => magicModal.hide('close button pressed')}
        style={styles.btn}
        icon={CloseImg}
      />
      <ModalInput value={searchValue} label={label} onChangeText={onChangeSearchValue} />
      <ModalList data={items} />
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: COLORS.transparent,
    margin: 16,
    position: 'relative'
  },
  btn: {
    position: 'absolute',
    top: -50,
    right: -20,
    width: 30,
    height: 29
  },
  img: {
    width: 30, height: 29
  }
})
