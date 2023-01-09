import { StyleSheet, Text } from 'react-native'
import React from 'react'
import { COLORS, SIZES } from '../../../constants'

export const FormContainer = () => (
  <Text style={styles.text}>Невролог-терапевт</Text>
)

const styles = StyleSheet.create({
  text: {
    textAlign: 'center',
    fontSize: SIZES.fs16,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
    fontFamily: 'Roboto-Italic'
  },
})
