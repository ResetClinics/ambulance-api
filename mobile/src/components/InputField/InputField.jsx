import React from 'react'
import { StyleSheet } from 'react-native'
import { Field } from 'react-final-form'
import { TextInput } from 'react-native-paper'
import { COLORS } from '../../../constants'

export const InputField = ({
  name, label, placeholder, secureTextEntry = null
}) => (
  <Field
    name={name}
  >
    {({ input }) => (
      <TextInput
        style={styles.input}
        mode="outlined"
        focused
        placeholder={placeholder}
        label={label}
        value={input.value}
        onChangeText={input.onChange}
        secureTextEntry={secureTextEntry}
      />
    )}
  </Field>
)

const styles = StyleSheet.create({
  input: {
    backgroundColor: COLORS.white,
    marginVertical: 8,
    color: COLORS.primary,
    width: '100%'
  }
})
