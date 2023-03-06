import React, { useEffect, useRef } from 'react'
import {
  Animated, Keyboard, KeyboardAvoidingView, Platform, StyleSheet, View,
} from 'react-native'
import { FormContainer, Layout } from '../../components'
import { COLORS } from '../../../constants'
import logoImg from '../../../assets/icon.png'

export const SignInScreen = ({ navigation }) => {
  const imageHeight = useRef(new Animated.Value(116)).current

  const heightUp = () => {
    Animated.timing(imageHeight, {
      toValue: 116,
      duration: 200,
      useNativeDriver: false,
    }).start()
  }

  const heightDown = () => {
    Animated.timing(imageHeight, {
      toValue: 80,
      duration: 200,
      useNativeDriver: false,
    }).start()
  }

  useEffect(() => {
    const showSubscription = Keyboard.addListener('keyboardDidShow', heightDown)
    const hideSubscription = Keyboard.addListener('keyboardDidHide', heightUp)

    return () => {
      showSubscription.remove()
      hideSubscription.remove()
    }
  }, [])

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={[styles.container, styles.root]}
    >
      <Layout>
        <View style={styles.inner}>
          <Animated.Image source={logoImg} resizeMode="contain" style={[styles.img, { height: imageHeight }]} />
          <FormContainer navigation={navigation} />
        </View>
      </Layout>
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.white,
    paddingTop: 130,
  },
  inner: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  img: {
    maxWidth: 116,
    maxHeight: 116,
    marginBottom: 25
  }
})
