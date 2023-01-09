import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Form } from 'react-final-form'
import {
  InputField, Layout, Logo
} from '../../components'

export const PasswordForgetScreen = () => (
  <Layout>
    <Logo />
    <Form
      onSubmit="onSubmit"
      render={() => (
        <View style={styles.container}>
          <View>
            <InputField name="accLogin" label="Логин" placeholder="Ваше имя пользователя" />
          </View>
          <View style={styles.wrap}>
            <Button
              mode="contained"
            >
              Восстановить пароль
            </Button>
          </View>
        </View>
      )}
    />
  </Layout>
)

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
})
