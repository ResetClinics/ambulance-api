import React from 'react'
import { Form } from 'react-final-form'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { COLORS } from '../../../constants'
import { InputField } from '../InputField'

export const FormContainer = ({ navigation, onSignIn }) => {
  const onSubmit = (values) => {
    console.log(values)
  }
  const validate = (values) => {
    console.log(values)
  }
  return (
    <Form
      onSubmit={onSubmit}
      validate={validate}
      render={() => (
        <View style={styles.container}>
          <View>
            <InputField name="login" label="Логин" placeholder="Ваше имя пользователя" />
            <InputField name="password" secureTextEntry label="Пароль" placeholder="Ваш пароль" />
          </View>
          <View style={styles.wrap}>
            <Button
              style={styles.btn}
              onPress={() => navigation.navigate('Восстановление пароля')}
              buttonColor={COLORS.white}
            >
              Забыли пароль?
            </Button>
            <Button
              mode="contained"
              onPress={onSignIn}
            >
              Войти
            </Button>
          </View>
        </View>
      )}
    />
  )
}
const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'space-between',
    width: '100%',
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
