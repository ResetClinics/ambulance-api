import { StatusBar, View, Text } from 'react-native';
import React, { useState } from 'react'
import {Post} from "./mobile/components/Post";
import {Footer, Header} from "./mobile/components";
export default function App() {
  return (
    <View>
      <Header />
      <Post title="Test" />
      <StatusBar theme="auto"/>
      <Text>1111</Text>
      <Footer />
    </View>
  );
}
