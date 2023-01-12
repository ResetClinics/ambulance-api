import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import { TeamNotAssigned } from './TeamNotAssigned'
import { TeamAssigned } from './TeamAssigned'
import { TeamAccepted } from './TeamAccepted'
import {BottomNavigation, Layout} from "../../components";
import {StyleSheet, View} from "react-native";
import {COLORS, FONTS} from "../../../constants";

const Stack = createNativeStackNavigator()

export const Team = ({navigation}) => (
  <View style={styles.root}>
    <Stack.Navigator>
      <Stack.Screen name="Главная Бригады" component={TeamNotAssigned} options={{ headerShown: false }} />
      <Stack.Screen name="Состав Бригады" component={TeamAssigned} options={{ headerShown: false }} />
      <Stack.Screen name="Подвержденная Бригада" component={TeamAccepted} options={{ headerShown: false }} />
    </Stack.Navigator>
    <BottomNavigation navigation={navigation} />
  </View>
)

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: COLORS.white
  },
})
