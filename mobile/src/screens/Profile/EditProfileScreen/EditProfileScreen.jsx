import React, { useState } from 'react'
import {
  View,
  StyleSheet, Platform,
} from 'react-native'
import { Avatar, Button } from 'react-native-paper'
import { KeyboardAwareScrollView } from 'react-native-keyboard-aware-scroll-view'
import { Form } from 'react-final-form'
import { onChange } from 'react-native-reanimated'
import { InputField, Layout } from '../../../components'
import defaultImg from '../../../../assets/images/default.png'
import { COLORS } from '../../../../constants'

export const EditProfileScreen = ({ navigation }) => {
  const [phone, setPhone] = useState('')
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

  const onSubmit = () => {
    navigation.navigate('profileScreen')
  }

  return (
    <KeyboardAwareScrollView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.root}
    >
      <Layout>
        <Form
          onSubmit={onSubmit}
          render={({ handleSubmit }) => (
            <View style={styles.container}>
              <View>
                <Avatar.Image size={120} source={defaultImg} style={styles.img} />
                <InputField name="name" label="Фамилия Имя Отчество" placeholder="Фамилия Имя Отчество" />
                <InputField name="speciality" label="Должность" placeholder="Должность" />
                <InputField
                  label="Номер телефона"
                  value={phone}
                  name="phone"
                  placeholder="Ваш номер телефона"
                  onChangeText={changeText}
                  mask={phoneMask}
                />
                <InputField name="password" label="Пароль" placeholder="Ваш пароль" />
              </View>
              <Button
                mode="contained"
                onPress={handleSubmit}
                style={styles.btn}
              >
                Сохранить
              </Button>
            </View>
          )}
        />
      </Layout>
    </KeyboardAwareScrollView>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: COLORS.white
  },
  img: {
    marginBottom: 10,
    backgroundColor: COLORS.transparent
  },
  container: {
    flex: 1,
    paddingBottom: 20
  },
  btn: {
    marginTop: 16
  }
})
