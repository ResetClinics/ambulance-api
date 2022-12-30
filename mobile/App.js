import { AppRegistry, View } from 'react-native';
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper';
import React, {useCallback, useEffect, useState} from 'react';
import { expo } from './app.json';
import { Main } from "./src/Main/Main";
import { MagicModalPortal } from 'react-native-magic-modal';
import * as Font from 'expo-font';
import * as SplashScreen from "expo-splash-screen";

export default function App() {
  const [appIsReady, setAppIsReady] = useState(false);

  useEffect(() => {
    async function prepare() {
      try {
        await SplashScreen.preventAutoHideAsync();
        await new Promise(resolve => setTimeout(resolve, 2000));
        await Font.loadAsync(
          {
            'Roboto-Regular': require('./assets/fonts/Roboto-Regular.ttf'),
            'Roboto-Medium': require('./assets/fonts/Roboto-Medium.ttf'),
            'Roboto-Bold': require('./assets/fonts/Roboto-Bold.ttf'),
            'Roboto-Italic': require('./assets/fonts/Roboto-Italic.ttf'),
          }
        );
      } catch (e) {
        console.warn(e);
      } finally {
        setAppIsReady(true);
      }
    }
    prepare();
  }, []);

  const onLayoutRootView = useCallback(async () => {
    if (appIsReady) {
      await SplashScreen.hideAsync();
    }
  }, [appIsReady]);


  if (!appIsReady) {
    return null;
  }

  return (
    <View onLayout={onLayoutRootView} style={{flex: 1}}>
      <PaperProvider theme={theme}>
        <MagicModalPortal />
        <Main />
      </PaperProvider>
    </View>
  );
}

AppRegistry.registerComponent(expo.name, () => App)

const theme = {
  ...MD3LightTheme, // or MD3DarkTheme
  roundness: 1,
  colors: {
    ...MD3LightTheme.colors,
    primary: '#04607A',
    secondary: 'rad',
    tertiary: 'red',
  },
};
