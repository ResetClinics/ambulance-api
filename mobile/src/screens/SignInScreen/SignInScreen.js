import React from 'react'
import { StyleSheet, View } from 'react-native'
import { FormContainer, Layout, Logo } from '../../components'

export const SignInScreen = ({ onSignIn, navigation }) => (
  <Layout>
    <View style={styles.root}>
      <Logo />
      <FormContainer onSignIn={onSignIn} navigation={navigation} />
    </View>
  </Layout>
)

const styles = StyleSheet.create({
  root: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 130
  },
})
