import React, { useState } from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Form } from 'react-final-form'
import {
  InputField, Layout, Logo
} from '../../components'
import { COLORS } from '../../../constants'

export const PasswordForgetScreen = ({ navigation }) => {
  const [name, setName] = useState('')
  return (
    <Layout>
      <View style={styles.root}>
        <Logo />
        <Form
          onSubmit="onSubmit"
          render={() => (
            <View style={styles.container}>
              <InputField
                name="accLogin"
                label="Логин"
                placeholder="Ваше имя пользователя"
                value={name}
                onChangeText={(value) => setName(value)}
              />
              <View style={styles.wrap}>
                <Button
                  style={styles.btn}
                  onPress={() => navigation.navigate('Sign In')}
                  buttonColor={COLORS.white}
                >
                  Авторизоваться
                </Button>
                <Button
                  mode="contained"
                  onPress={() => navigation.navigate('isSent')}
                >
                  Восстановить пароль
                </Button>
              </View>
            </View>
          )}
        />
      </View>
    </Layout>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 27
  },
  container: {
    flex: 1,
    justifyContent: 'space-between',
    width: '100%',
    marginTop: 78
  },
  wrap: {
    width: '100%',
  },
  btn: {
    marginBottom: 10,
    alignItems: 'flex-start',
    marginLeft: -12
  },
})
