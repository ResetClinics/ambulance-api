import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import { Layout, Logo } from '../../components'
import { COLORS, FONTS } from '../../../constants'

export const Sent = () => (
  <Layout>
    <View style={styles.root}>
      <Logo />
      <Text style={styles.text}>Письмо с восстановлением пароля отправлено Вам на почту</Text>
    </View>
  </Layout>
)

const styles = StyleSheet.create({
  root: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: {
    textAlign: 'center',
    ...FONTS.title,
    maxWidth: '75%',
    marginTop: 30,
    color: COLORS.primary
  }
})
