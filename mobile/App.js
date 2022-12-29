import { AppRegistry } from 'react-native';
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper';
import React, {useState} from 'react';
import { expo } from './app.json';
import { Main } from "./src/Main/Main";
import { MagicModalPortal } from 'react-native-magic-modal';
import * as Font from 'expo-font';
import AppLoading from 'expo-app-loading';

async function loadApplication() {
  await Font.loadAsync({
    'Roboto-Regular': require('./assets/fonts/Roboto-Regular.ttf'),
    'Roboto-Medium': require('./assets/fonts/Roboto-Medium.ttf'),
    'Roboto-Bold': require('./assets/fonts/Roboto-Bold.ttf'),
  });
}

export default function App() {
  const [isReady, setIsReady] = useState(false);

  if (!isReady) {
    return (
      <AppLoading
        startAsync={loadApplication}
        onError={err => console.log(err)}
        onFinish={() => setIsReady(true)}
      />
    )
  }

  return (
    <PaperProvider theme={theme}>
      <MagicModalPortal />
      <Main />
    </PaperProvider>
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
