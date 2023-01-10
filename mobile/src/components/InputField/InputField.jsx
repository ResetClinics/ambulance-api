import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import { Field } from 'react-final-form'
import { TextInput } from 'react-native-paper'
import { COLORS, FONTS } from '../../../constants'

export const InputField = ({
  name, label, placeholder, secureTextEntry = null
}) => (
  <Field
    name={name}
  >
    {({ input, meta }) => (
      <View>
        <TextInput
          style={styles.input}
          mode="outlined"
          focused
          placeholder={placeholder}
          label={label}
          value={input.value}
          onChangeText={input.onChange}
          secureTextEntry={secureTextEntry}
          error={meta.touched && meta.error}
        />

        {meta.touched && meta.error && <Text style={styles.error}>{meta.error}</Text>}
      </View>
    )}
  </Field>
)

const styles = StyleSheet.create({
  input: {
    backgroundColor: COLORS.white,
    marginVertical: 8,
    color: COLORS.primary,
    width: '100%'
  },
  error: {
    ...FONTS.smallText,
    color: COLORS.error,
    marginLeft: 16
  }
})
