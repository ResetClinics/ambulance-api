import React from 'react'
import { Form } from 'react-final-form'
import * as Yup from 'yup'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { COLORS } from '../../../constants'
import { InputField } from '../InputField'
import useValidationSchema from '../helper/use-validation-schema'

export const FormContainer = ({ navigation, onSignIn }) => {
  const [phone, setPhone] = React.useState('')
  const phoneMask = ['(', /\d/, /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/]

  const onSubmit = () => {
    onSignIn()
  }
  const schema = Yup.object().shape({
    phone: Yup.string().required('Неверный номер телефона'),
    password: Yup.string().required('Неверный пароль'),
  })
  const validate = useValidationSchema(schema)
  return (
    <Form
      onSubmit={onSubmit}
      validate={validate}
      render={({ handleSubmit }) => (
        <View style={styles.container}>
          <View>
            <InputField
              value={phone}
              name="phone"
              selectTextOnFocus="Номер телефона"
              placeholder="Ваш номер телефона"
              onChangeText={(masked) => {
                setPhone(masked)
              }}
              mask={phoneMask}
            />
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
              onPress={handleSubmit}
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
    maxHeight: '68%',
  },
  wrap: {
    width: '100%',
  },
  btn: {
    marginBottom: 10,
    alignItems: 'flex-start',
  },
})
