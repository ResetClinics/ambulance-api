import React from 'react'
import {
  View, Text, StyleSheet, Button
} from 'react-native'

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
})

export const SignInScreen = ({ onSignIn, navigation }) => (
  <View style={styles.container}>
    <Button title="Sign In" onPress={onSignIn} />
    <Text>OR</Text>
    <Button
      title="Go to Password Forget"
      onPress={() => navigation.navigate('Password Forget')}
    />
  </View>
)
