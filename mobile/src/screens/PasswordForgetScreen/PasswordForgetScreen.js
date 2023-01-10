import React from 'react'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { Form } from 'react-final-form'
import * as Yup from 'yup'
import {
  InputField, Layout, Logo
} from '../../components'
import { COLORS } from '../../../constants'
import useValidationSchema from '../../components/helper/use-validation-schema'

export const PasswordForgetScreen = ({ navigation }) => {
  const onSubmit = () => {
    navigation.navigate('isSent')
  }
  const schema = Yup.object().shape({
    login: Yup.string().required('Неверный логин'),
  })
  const validate = useValidationSchema(schema)
  return (
    <Layout>
      <View style={styles.root}>
        <Logo />
        <Form
          style={styles.root}
          onSubmit={onSubmit}
          validate={validate}
          render={({ handleSubmit }) => (
            <View style={styles.container}>
              <View>
                <InputField name="login" label="Логин" placeholder="Ваше имя пользователя" />
              </View>
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
                  onPress={handleSubmit}
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
