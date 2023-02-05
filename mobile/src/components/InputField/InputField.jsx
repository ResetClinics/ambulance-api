import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import { Field } from 'react-final-form'
import { InternalTheme, MD3LightTheme, TextInput, } from 'react-native-paper'
import MaskInput from 'react-native-mask-input'
import { COLORS, FONTS } from '../../../constants'

export const InputField = ({
  name, label, placeholder, mask = false, secureTextEntry = null
}) => (
  <Field
    name={name}
  >
    {({ input, meta }) => {
      if (mask) {
        return (
          <View>
            <MaskInput
              style={[styles.inputMask, styles.input]}
              mode="outlined"
              focused
              placeholder={placeholder}
              label={label}
              value={input.value}
              onChangeText={input.onChange}
              secureTextEntry={secureTextEntry}
              error={meta.touched && meta.error}
              mask={mask}
              placeholderTextColor={COLORS.primary}
              cursorColor={COLORS.primary}
              onFocus={() => console.log('onfocus')}
            />
            {meta.touched && meta.error && <Text style={styles.error}>{meta.error}</Text>}
          </View>
        )
      }
      return (
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
      )
    }}
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
  inputMask: {
    color: COLORS.primary,
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderColor: COLORS.primary,
    borderWidth: 1,
    borderRadius: 5,
    placeholderTextColor: COLORS.primary,
  },
  error: {
    ...FONTS.smallText,
    color: COLORS.error,
    marginLeft: 16
  }
})

const theme = {
  ...InternalTheme,
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
