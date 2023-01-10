import React from 'react'
import { StyleSheet, View, TextInput } from 'react-native'
import { COLORS, FONTS } from '../../../../constants'

export const ModalInput = ({ label }) => {
  const [text, setText] = React.useState('')
  return (
    <View style={styles.container}>
      <TextInput
        style={styles.input}
        placeholder={label}
        label={label}
        value={text}
        onChangeText={(value) => setText(value)}
      />
    </View>
  )
}
const styles = StyleSheet.create({
  container: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    width: '100%'
  },
  input: {
    ...FONTS.text,
    padding: 15
  }
})
