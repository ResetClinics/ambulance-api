import { AppRegistry } from 'react-native';
import { Provider as PaperProvider, MD3LightTheme } from 'react-native-paper';
import React from 'react';
import {expo} from './app.json';
import { Main } from "./src/Main/Main";

export default function App() {
  return (
    <PaperProvider theme={theme}>
      <Main />
    </PaperProvider>
  );
}
AppRegistry.registerComponent(expo.name, () => App)

console.log(MD3LightTheme.colors)
for (let key in MD3LightTheme.fonts) {
  console.log(key)
}

const theme = {
  ...MD3LightTheme, // or MD3DarkTheme
  roundness: 1,
  colors: {
    ...MD3LightTheme.colors,
    primary: '#04607A',
    secondary: 'rad',
    tertiary: '#a1b2c3',
  },
};
