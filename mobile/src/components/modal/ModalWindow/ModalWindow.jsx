import React from 'react'
import { Image, StyleSheet, View } from 'react-native'
import { magicModal } from 'react-native-magic-modal'
import { Button } from 'react-native-paper'
import { ModalInput } from '../ModalInput'
import { ModalList } from '../ModalList'
import closeImg from '../../../../assets/images/close.webp'
import { COLORS } from '../../../../constants'

const data = [
  {
    name: 'Услуга',
    id: 1
  },
  {
    name: 'Услуга-2',
    id: 1
  },
  {
    name: 'Услуга-3',
    id: 1
  },
  {
    name: 'Услуга',
    id: 1
  },
  {
    name: 'Услуга-2',
    id: 1
  },
  {
    name: 'Услуга-3',
    id: 1
  },
  {
    name: 'Услуга',
    id: 1
  },
  {
    name: 'Услуга-2',
    id: 1
  },
  {
    name: 'Услуга-3',
    id: 1
  },
  {
    name: 'Услуга',
    id: 1
  },
  {
    name: 'Услуга-2',
    id: 1
  },
  {
    name: 'Услуга-3',
    id: 1
  },
]

export const ModalWindow = ({ label }) => (
  <View style={styles.container}>
    <Button
      onPress={() => magicModal.hide('close button pressed')}
      style={styles.btn}
      icon={() => (
        <Image
          source={closeImg}
          style={styles.img}
        />
      )}
    />
    <ModalInput label={label} />
    <ModalList data={data} />
  </View>
)

const styles = StyleSheet.create({
  container: {
    backgroundColor: COLORS.transparent,
    margin: 16,
    position: 'relative'
  },
  btn: {
    position: 'absolute',
    top: -50,
    right: -20
  },
  img: {
    width: 30, height: 29
  }
})
