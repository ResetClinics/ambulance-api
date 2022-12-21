import { StatusBar, View, Text } from 'react-native';
import React from 'react'
import {Footer, Header} from "./mobile/components";
export default function App() {
  return (
    <View>
      <StatusBar theme="auto"/>
      <Header />
      <Footer />
    </View>
  );
}
