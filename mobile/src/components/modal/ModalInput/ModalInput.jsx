import { TextInput } from 'react-native-paper'
import React from 'react'
import { StyleSheet } from 'react-native'
import { COLORS, SIZES } from '../../../../constants'

export const ModalInput = ({ label }) => {
  const [text, setText] = React.useState('')
  return (
    <TextInput
      style={styles.input}
      label={label}
      value={text}
      onChangeText={(value) => setText(value)}
    />
  )
}
const styles = StyleSheet.create({
  input: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    fontSize: SIZES.fs16,
  }
})
