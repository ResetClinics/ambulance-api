import React from 'react'
import { FormContainer, Layout, Logo } from '../../components'

export const SignInScreen = ({ onSignIn, navigation }) => (
  <Layout>
    <Logo />
    <FormContainer onSignIn={onSignIn} navigation={navigation} />
  </Layout>
)
