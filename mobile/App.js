import { AppRegistry } from 'react-native';
import { Provider as PaperProvider } from 'react-native-paper';
import React from 'react';
import {expo} from './app.json';
import { Main } from "./src/Main/Main";

export default function App() {
  return (
    <PaperProvider>
      <Main />
    </PaperProvider>
  );
}
AppRegistry.registerComponent(expo.name, () => App)
