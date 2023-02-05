import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import { Button } from 'react-native-paper'
import { Layout, Logo } from '../../components'
import { COLORS, FONTS } from '../../../constants'

export const Sent = ({ navigation }) => (
  <Layout>
    <View style={styles.root}>
      <Logo />
      <Text style={styles.text}>Письмо с восстановлением пароля отправлено Вам на почту</Text>
      <Button
        onPress={() => navigation.navigate('Sign In')}
        mode="contained"
      >
        Авторизоваться
      </Button>
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
    color: COLORS.primary,
    marginBottom: 50
  }
})
