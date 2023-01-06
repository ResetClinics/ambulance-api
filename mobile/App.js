import { AppRegistry, StyleSheet, View } from 'react-native'
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper'
import React, { useCallback, useEffect, useState } from 'react'
import { MagicModalPortal } from 'react-native-magic-modal'
import * as Font from 'expo-font'
import * as SplashScreen from 'expo-splash-screen'
import { Main } from './src/Main/Main'
import { expo } from './app.json'
import regularFont from './assets/fonts/Roboto-Regular.ttf'
import mediumFont from './assets/fonts/Roboto-Medium.ttf'
import boldFont from './assets/fonts/Roboto-Bold.ttf'
import italicFont from './assets/fonts/Roboto-Italic.ttf'

export const App = () => {
  const [appIsReady, setAppIsReady] = useState(false)

  useEffect(() => {
    async function prepare() {
      try {
        await SplashScreen.preventAutoHideAsync()
        await Font.loadAsync(
          {
            'Roboto-Regular': { regularFont },
            'Roboto-Medium': { mediumFont },
            'Roboto-Bold': { boldFont },
            'Roboto-Italic': { italicFont },
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
        <Main />
      </PaperProvider>
    </View>
  )
}

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
