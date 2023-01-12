import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import {StyleSheet, View} from 'react-native'
import { CurrentCallPage } from './CurrentCallPage'
import { Map } from './Map'
import { BottomNavigation } from '../../components'
import { COLORS } from '../../../constants'

const Stack = createNativeStackNavigator()
export const CurrentCall = ({ navigation }) => (
  <View style={styles.root}>
    <Stack.Navigator>
      <Stack.Screen name="Home" component={CurrentCallPage} options={{ title: 'Текущий вызов' }} />
      <Stack.Screen name="Маршрут" component={Map} options={{ title: 'Маршрут до места вызова' }} />
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
