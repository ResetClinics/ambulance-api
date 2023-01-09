import { AppRegistry, StyleSheet, View } from 'react-native'
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper'
import React, { useCallback, useEffect, useState } from 'react'
import { MagicModalPortal } from 'react-native-magic-modal'
import * as Font from 'expo-font'
import * as SplashScreen from 'expo-splash-screen'
import { expo } from './app.json'
import italicFont from './assets/fonts/Roboto-Italic.ttf'
import boldFont from './assets/fonts/Roboto-Bold.ttf'
import regularFont from './assets/fonts/Roboto-Regular.ttf'
import mediumFont from './assets/fonts/Roboto-Medium.ttf'
import { Routes } from './src/navigation/Routes'

const App = () => {
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
            'Roboto-Italic': italicFont,
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
    <View onLayout={onLayoutRootView} style={styles.root}>
      <PaperProvider theme={theme}>
        <MagicModalPortal />
        <Routes />
      </PaperProvider>
    </View>
  )
}

export default App

AppRegistry.registerComponent(expo.name, () => App)

const styles = StyleSheet.create({
  root: {
    flex: 1
  }
})

const theme = {
  ...MD3LightTheme, // or MD3DarkTheme
  roundness: 1,
  colors: {
    ...MD3LightTheme.colors,
    primary: '#04607A',
    secondary: 'rad',
    tertiary: 'red',
  },
}
