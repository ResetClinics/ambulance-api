import React, { useContext } from 'react'
import { Form } from 'react-final-form'
import * as Yup from 'yup'
import { View, StyleSheet } from 'react-native'
import { Button } from 'react-native-paper'
import { onChange } from 'react-native-reanimated'
import { COLORS } from '../../../constants'
import { InputField } from '../InputField'
import useValidationSchema from '../helper/use-validation-schema'
import { AuthContext } from '../../context/AuthContext'

export const FormContainer = ({ navigation }) => {
  const { login } = useContext(AuthContext)
  const [phone, setPhone] = React.useState('')
  const phoneMask = ['+', '7', ' ', '(', /\d/, /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ' ', /\d/, /\d/]

  const changeText = (masked, unmasked) => {
    if (unmasked[0] === '9') {
      const changed = unmasked.replace('9', '7 (9')
      setPhone(changed)
      onChange(changed)
      return
    }
    setPhone(unmasked)
    onChange(masked)
  }
  const schema = Yup.object().shape({
    phone: Yup.string().required('Неверный номер телефона'),
    password: Yup.string().required('Неверный пароль'),
  })
  const validate = useValidationSchema(schema)
  return (
    <Form
      onSubmit={login}
      validate={validate}
      render={({ handleSubmit }) => (
        <View style={styles.container}>
          <View>
            <InputField
              label="Номер телефона"
              value={phone}
              name="phone"
              placeholder="Ваш номер телефона"
              onChangeText={changeText}
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
