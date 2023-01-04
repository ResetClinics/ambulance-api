import React from 'react'
import { Image, StyleSheet, View } from 'react-native'
import { magicModal } from 'react-native-magic-modal'
import { Button } from 'react-native-paper'
import { ModalInput } from '../ModalInput'
import { ModalList } from '../ModalList'

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
          source={require('../../../../assets/images/close.webp')}
          style={{ width: 30, height: 29 }}
        />
      )}
    />
    <ModalInput label={label} />
    <ModalList data={data} />
  </View>
)

const styles = StyleSheet.create({
  container: {
    backgroundColor: 'transparent',
    margin: 16,
    position: 'relative'
  },
  btn: {
    position: 'absolute',
    top: -50,
    right: -20
  },
})
