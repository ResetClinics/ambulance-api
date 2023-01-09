import React from 'react'
import {
  View, StyleSheet, Image,
} from 'react-native'
import {
  Button
} from 'react-native-paper'
import { FormContainer, Layout } from '../../components'
import { COLORS } from '../../../constants'
import logoImg from '../../../assets/logo.webp'

export const SignInScreen = ({ onSignIn, navigation }) => (
  <Layout>
    <View style={styles.container}>
      <View>
        <Image
          resizeMode="contain"
          source={logoImg}
          style={styles.img}
        />
        <FormContainer />
      </View>
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
  </Layout>
)

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  wrap: {
    width: '100%',
  },
  btn: {
    marginBottom: 10,
    alignItems: 'flex-start',
    marginLeft: -12
  },
  img: {
    minWidth: 116,
    maxHeight: 116,
    marginVertical: '15%'
  }
})
