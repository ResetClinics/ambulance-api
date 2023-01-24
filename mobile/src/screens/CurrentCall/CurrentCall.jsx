import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import { CurrentCallPage } from './CurrentCallPage'
import { Map } from './Map'
import { BottomNavigation, ScreenLayout } from '../../components'

const Stack = createNativeStackNavigator()
export const CurrentCall = ({ navigation }) => (
  <ScreenLayout>
    <Stack.Navigator>
      <Stack.Screen name="Home" component={CurrentCallPage} options={{ headerShown: false }} />
      <Stack.Screen name="itinerary" component={Map} options={{ title: 'Маршрут до места вызова' }} />
    </Stack.Navigator>
    <BottomNavigation navigation={navigation} />
  </ScreenLayout>
)
