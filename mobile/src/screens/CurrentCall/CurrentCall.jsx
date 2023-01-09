import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import { CurrentCallPage } from './CurrentCallPage'
import { Map } from './Map'

const Stack = createNativeStackNavigator()
export const CurrentCall = () => (
  <Stack.Navigator>
    <Stack.Screen name="Home" component={CurrentCallPage} options={{ title: 'Текущий вызов' }} />
    <Stack.Screen name="Маршрут" component={Map} options={{ title: 'Маршрут до места вызова' }} />
  </Stack.Navigator>
)
