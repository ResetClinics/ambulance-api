import React from "react";
import { Map, CurrentCallPage } from "../../components";
import { createNativeStackNavigator } from "@react-navigation/native-stack";

const Stack = createNativeStackNavigator()
export const CurrentCall = () => {
  return (
    <Stack.Navigator>
      <Stack.Screen name="Home" component={CurrentCallPage} options={{title: ''}} />
      <Stack.Screen name="Маршрут" component={Map} options={{title: 'Маршрут'}} />
    </Stack.Navigator>
  )
}
