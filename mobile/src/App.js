import {
  Alert,
  AppRegistry, SafeAreaView, StyleSheet,
} from 'react-native'
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper'
import React, { useCallback, useEffect, useState } from 'react'
import * as Font from 'expo-font'
import * as SplashScreen from 'expo-splash-screen'
import messaging from '@react-native-firebase/messaging'
import { expo } from '../app.json'
import redRegularFont from '../assets/fonts/RedRing-Regular.ttf'
import boldFont from '../assets/fonts/Roboto-Bold.ttf'
import regularFont from '../assets/fonts/Roboto-Regular.ttf'
import mediumFont from '../assets/fonts/Roboto-Medium.ttf'
import { COLORS } from '../constants'
import { Routes } from './navigation'
import { AuthProvider } from './context/AuthContext'
import { CurrentCallingProvider } from './context/CurrentCallingContext'

const App = () => {

  const requestUserPermission = async () => {
    const authStatus = await messaging().requestPermission()
    const enabled = authStatus === messaging.AuthorizationStatus.AUTHORIZED
      || authStatus === messaging.AuthorizationStatus.PROVISIONAL

    if (enabled) {
      console.log('Authorization status:', authStatus)
    }
  }

  useEffect(
    () => {
      if (requestUserPermission()) {
        messaging().getToken().then((token) => {
          console.log(token)
        })
      } else {
        console.log('Faled token status')
      }

      messaging()
        .getInitialNotification()
        .then(async (remoteMessage) => {
          if (remoteMessage) {
            console.log(
              'Notification caused app to open from quit state:',
              remoteMessage.notification,
            )
          }
        })

      messaging().onNotificationOpenedApp((remoteMessage) => {
        console.log(
          'Notification caused app to open from background state:',
          remoteMessage.notification,
        )
      })

      messaging().setBackgroundMessageHandler(async (remoteMessage) => {
        console.log('Message handled in the background!', remoteMessage)
      })

      const unsubscribe = messaging().onMessage(async (remoteMessage) => {
        Alert.alert('A new FCM message arrived!', JSON.stringify(remoteMessage))
      })

      return unsubscribe

    },
    []
  )

  const [appIsReady, setAppIsReady] = useState(false)

  useEffect(() => {
    async function prepare() {
      try {
        await SplashScreen.preventAutoHideAsync()
        // eslint-disable-next-line no-promise-executor-return
        await new Promise((resolve) => setTimeout(resolve, 2000))
        await Font.loadAsync(
          {
            'Roboto-Regular': regularFont,
            'Roboto-Medium': mediumFont,
            'Roboto-Bold': boldFont,
            'RedRing-Regular': redRegularFont
          }
        )
      } catch (e) {
        console.warn(e)
      } finally {
        setAppIsReady(true)
      }
    }

    prepare()
  }, [])

  const onLayoutRootView = useCallback(async () => {
    if (appIsReady) {
      await SplashScreen.hideAsync()
    }
  }, [appIsReady])

  if (!appIsReady) {
    return null
  }

  return (
    <SafeAreaView onLayout={onLayoutRootView} style={styles.root}>
      <PaperProvider theme={theme}>
        <AuthProvider>
          <CurrentCallingProvider>
            <Routes />
          </CurrentCallingProvider>
        </AuthProvider>
      </PaperProvider>
    </SafeAreaView>
  )
}

export default App

AppRegistry.registerComponent(expo.name, () => App)

const styles = StyleSheet.create({
  root: {
    flex: 1,
  },
})

const theme = {
  ...MD3LightTheme,
  roundness: 1,
  colors: {
    ...MD3LightTheme.colors,
    primary: COLORS.primary,
    secondary: 'rad',
    tertiary: 'red',
    outline: COLORS.primary,
  },
}
