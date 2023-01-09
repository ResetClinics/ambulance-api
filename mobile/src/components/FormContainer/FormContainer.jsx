import React from 'react'
import { Form, Field } from 'react-final-form'
import { View, Text, StyleSheet } from 'react-native'
import { Button, TextInput } from 'react-native-paper'
import { COLORS } from '../../../constants'

export const FormContainer = ({ navigation, onSignIn }) => {
  const [text, setText] = React.useState('')

  const onSubmit = () => {
    console.log('ok')
  }
  const validate = () => {
    console.log('ok')
  }
  return (
    <Form
      onSubmit={onSubmit}
      validate={validate}
      render={() => (
        <View style={styles.container}>
          <Field
            name="FieldName"
            placeholder="your placeholder"
          >
            {({ input, meta, placeholder }) => (
              <View>
                <TextInput
                  placeholder={placeholder}
                  /* eslint-disable-next-line react/jsx-props-no-spreading */
                  {...input}
                  label="Email"
                  value={text}
                  onChangeText={(value) => setText(value)}
                />
                <Text size={8}>{meta.error}</Text>
              </View>
            )}
          </Field>
          <View style={styles.wrap}>
            <Button
              style={styles.btn}
              onPress={() => navigation.navigate('Password Forget')}
              buttonColor={COLORS.white}
            >
              Забыли пароль?
            </Button>
            <Button
              onPress={onSignIn}
              mode="contained"
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
    width: '100%'
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
