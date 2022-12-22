import {SafeAreaView, StatusBar, View} from 'react-native';
import React from 'react';
import { Footer, Header } from "./src/components";
export default function App() {
  return (
    <SafeAreaView>
      <View>
        <StatusBar theme="auto"/>
        <Header />
        <Footer />
      </View>
    </SafeAreaView>
  );
}
