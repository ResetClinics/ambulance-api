import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import { Field } from 'react-final-form'
import { MD3LightTheme, TextInput } from 'react-native-paper'
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
          theme={theme}
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
    fontSize: 16,
    lineHeight: 24,
    letterSpacing: 0.15,
    width: '100%',
  },
  error: {
    ...FONTS.smallText,
    color: COLORS.error,
    marginLeft: 16
  }
})

const theme = {
  ...MD3LightTheme,
  roundness: 5,
  colors: {
    ...MD3LightTheme.colors,
    primary: COLORS.primary,
    outline: COLORS.primary,
    onSurface: COLORS.primary,
    error: COLORS.primary,
    onSurfaceVariant: COLORS.primary,
  },
}
