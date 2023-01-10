import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import { TeamNotAssigned } from './TeamNotAssigned'
import { TeamAssigned } from './TeamAssigned'
import { TeamAccepted } from './TeamAccepted'

const Stack = createNativeStackNavigator()

export const Team = () => (
  <Stack.Navigator>
    <Stack.Screen name="Главная Бригады" component={TeamNotAssigned} options={{ headerShown: false }} />
    <Stack.Screen name="Состав Бригады" component={TeamAssigned} options={{ headerShown: false }} />
    <Stack.Screen name="Подвержденная Бригада" component={TeamAccepted} options={{ headerShown: false }} />
  </Stack.Navigator>
)
